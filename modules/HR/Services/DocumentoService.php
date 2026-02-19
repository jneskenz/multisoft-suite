<?php

namespace Modules\HR\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Models\Contrato;
use Modules\HR\Models\DocumentoGenerado;
use Modules\HR\Models\PlantillaDocumento;
use Modules\HR\Models\PlantillaSeccionAsignada;

class DocumentoService
{
   /**
    * Genera un DocumentoGenerado a partir de un contrato y una plantilla.
    *
    * 1. Resuelve variables en el HTML de la plantilla
    * 2. Genera el PDF con DomPDF
    * 3. Guarda el archivo y crea el registro en hr_documentos_generados
    */
   public function generarDesdeContrato(
      Contrato $contrato,
      PlantillaDocumento $plantilla
   ): DocumentoGenerado {
      // Cargar relaciones necesarias
      $contrato->loadMissing(['empleado.company', 'empleado.groupCompany', 'tipoContrato']);
      $plantilla->loadMissing('tipoDocumento');

      // 1. Construir mapa de variables
      $variables = $this->buildVariables($contrato);

      // 2. Generar número de documento
      $numeroDocumento = $this->generarNumeroDocumento($plantilla);

      // Agregar variables del documento generado
      $variables['documento.numero'] = $numeroDocumento;
      $variables['documento.fecha_generacion'] = now()->format('d/m/Y');

      // 3. Resolver variables en el contenido de la plantilla
      $contenidoResuelto = $this->resolveVariables($plantilla->contenido_html ?? '', $variables);

      // 4. Cargar secciones asignadas y resolver variables
      $secciones = $this->loadSecciones($plantilla, $variables);

      // 5. Generar PDF
      $pdf = Pdf::loadView('hr::contratos.documento-generado', [
         'plantilla'       => $plantilla,
         'contenido'       => $contenidoResuelto,
         'seccionesInicio' => $secciones['inicio'],
         'seccionesCuerpo' => $secciones['cuerpo'],
         'seccionesFinal'  => $secciones['final'],
         'numeroDocumento' => $numeroDocumento,
      ]);

      $pdf->setPaper(
         strtolower($plantilla->formato_papel ?? 'a4'),
         $plantilla->orientacion === 'horizontal' ? 'landscape' : 'portrait'
      );

      $pdf->setOption('margin_top', 72);
      $pdf->setOption('margin_bottom', 60);
      $pdf->setOption('margin_left', 72);
      $pdf->setOption('margin_right', 56);

      // 6. Guardar PDF en disco
      $filename = sprintf(
         'contratos/%s/%s.pdf',
         $contrato->empleado_id,
         $numeroDocumento
      );
      Storage::disk('local')->put($filename, $pdf->output());

      // 7. Crear registro DocumentoGenerado
      $documento = DocumentoGenerado::create([
         'numero_documento'       => $numeroDocumento,
         'tipo_documento_id'      => $plantilla->tipo_documento_id,
         'categoria_documento_id' => $plantilla->categoria_documento_id,
         'plantilla_utilizada_id' => $plantilla->id,
         'plantilla_version'      => $plantilla->version ?? '1.0',
         'contenido_generado'     => $contenidoResuelto,
         'variables_aplicadas'    => $variables,
         'fecha_generacion'       => now(),
         'empleado_id'            => $contrato->empleado_id,
         'contrato_id'            => $contrato->id,
         'estado_firmas'          => 'pendiente',
         'ruta_archivo_pdf'       => $filename,
         'estado_documento'       => '0', // Borrador
         'estado'                 => '1', // Activo
         'fecha_vigencia_desde'   => $contrato->fecha_inicio,
         'fecha_vigencia_hasta'   => $contrato->fecha_fin,
         'creado_por'             => auth()->id(),
         'actualizado_por'        => auth()->id(),
      ]);

      return $documento;
   }

   /**
    * Construye el array de variables desde el contrato y sus relaciones.
    */
   public function buildVariables(Contrato $contrato): array
   {
      $empleado = $contrato->empleado;
      $company  = $empleado?->company;
      $group    = $empleado?->groupCompany;

      return array_filter([
         // ── Empleado ──
         'empleado.nombre_completo'  => $empleado?->nombre,
         'empleado.documento_tipo'   => $empleado?->documento_tipo,
         'empleado.documento_numero' => $empleado?->documento_numero,
         'empleado.email'            => $empleado?->email,
         'empleado.telefono'         => $empleado?->telefono,
         'empleado.cargo'            => $empleado?->cargo,
         'empleado.codigo'           => $empleado?->codigo_empleado,
         'empleado.fecha_ingreso'    => $empleado?->fecha_ingreso?->format('d/m/Y'),
         'empleado.direccion'        => '',

         // ── Contrato ──
         'contrato.numero'           => $contrato->numero_contrato,
         'contrato.tipo'             => $contrato->tipoContrato?->nombre,
         'contrato.modalidad'        => $contrato->modalidad?->nombre ?? '',
         'contrato.fecha_inicio'     => $contrato->fecha_inicio?->format('d/m/Y'),
         'contrato.fecha_fin'        => $contrato->fecha_fin?->format('d/m/Y') ?? 'Indefinido',
         'contrato.salario_base'     => $contrato->salario_base
            ? number_format((float) $contrato->salario_base, 2, '.', ',')
            : '',
         'contrato.moneda'           => $group?->currency_symbol ?? 'S/',
         'contrato.horario'          => $contrato->descripcion_horario ?? '',
         'contrato.horas_semanales'  => $contrato->horas_semanales ?? '',

         // ── Empresa ──
         'empresa.razon_social'        => $company?->name ?? $group?->business_name ?? '',
         'empresa.nombre_comercial'    => $company?->trade_name ?? $group?->trade_name ?? '',
         'empresa.ruc'                 => $company?->tax_id ?? $group?->tax_id ?? '',
         'empresa.direccion'           => $company?->address ?? $group?->address ?? '',
         'empresa.telefono'            => $company?->phone ?? $group?->phone ?? '',
         'empresa.email'               => $company?->email ?? $group?->email ?? '',
         'empresa.representante_legal' => '', // Puede ser llenado después
         'empresa.representante_dni'   => '',

         // ── Cargo ──
         'cargo.nombre'       => $empleado?->cargo ?? '',
         'cargo.departamento' => '',
         'cargo.nivel'        => '',
      ], fn($v) => $v !== null);
   }

   /**
    * Reemplaza todas las variables {{grupo.variable}} en el HTML.
    */
   public function resolveVariables(string $html, array $variables): string
   {
      if (empty($html)) {
         return $html;
      }

      foreach ($variables as $key => $value) {
         $html = str_replace('{{' . $key . '}}', (string) $value, $html);
      }

      // Limpiar variables no resueltas: reemplazar con texto placeholder
      $html = preg_replace('/\{\{([a-zA-Z0-9_.]+)\}\}/', '<span style="color:#c00;">[⚠ $1]</span>', $html);

      return $html;
   }

   /**
    * Carga las secciones asignadas a la plantilla y resuelve sus variables.
    */
   private function loadSecciones(PlantillaDocumento $plantilla, array $variables): array
   {
      $secciones = PlantillaSeccionAsignada::where('plantilla_id', $plantilla->id)
         ->join('hr_plantillas_secciones', 'hr_plantillas_secciones.id', '=', 'seccion_id')
         ->orderBy('hr_plantillas_secciones_asignadas.orden')
         ->get([
            'hr_plantillas_secciones.nombre',
            'hr_plantillas_secciones.contenido_html',
            'hr_plantillas_secciones_asignadas.ubicacion',
         ]);

      foreach ($secciones as $sec) {
         $sec->contenido_html = $this->convertQuillToInline($sec->contenido_html ?? '');
         $sec->contenido_html = $this->resolveVariables($sec->contenido_html, $variables);
         $sec->ubicacion = $sec->ubicacion ?: 'cuerpo';
      }

      return [
         'inicio' => $secciones->where('ubicacion', 'inicio'),
         'cuerpo' => $secciones->where('ubicacion', 'cuerpo'),
         'final'  => $secciones->where('ubicacion', 'final'),
      ];
   }

   /**
    * Genera un número de documento secuencial basado en la plantilla.
    */
   private function generarNumeroDocumento(PlantillaDocumento $plantilla): string
   {
      $prefijo = $plantilla->tipoDocumento?->prefijo_numeracion ?? 'DOC-';
      $year = now()->year;

      $last = DocumentoGenerado::where('numero_documento', 'like', "{$prefijo}{$year}-%")
         ->orderByDesc('numero_documento')
         ->value('numero_documento');

      $seq = $last ? (int) substr($last, -4) + 1 : 1;

      return sprintf('%s%d-%04d', $prefijo, $year, $seq);
   }

   /**
    * Convert Quill CSS classes to inline styles for DomPDF compatibility.
    * (Reutilizado de PlantillaController)
    */
   private function convertQuillToInline(string $html): string
   {
      if (empty($html)) return $html;

      $classToStyle = [
         'ql-align-center'  => 'text-align: center;',
         'ql-align-right'   => 'text-align: right;',
         'ql-align-justify' => 'text-align: justify;',
         'ql-size-small'    => 'font-size: 9pt;',
         'ql-size-large'    => 'font-size: 14pt;',
         'ql-size-huge'     => 'font-size: 18pt;',
         'ql-font-serif'    => "font-family: 'DejaVu Serif', Georgia, serif;",
         'ql-font-monospace' => "font-family: 'DejaVu Sans Mono', monospace;",
      ];

      for ($i = 1; $i <= 8; $i++) {
         $classToStyle['ql-indent-' . $i] = 'margin-left: ' . ($i * 24) . 'pt;';
      }

      $html = preg_replace_callback(
         '/<([a-z][a-z0-9]*)\b([^>]*?)class\s*=\s*"([^"]*)"([^>]*)>/i',
         function ($matches) use ($classToStyle) {
            $tag     = $matches[1];
            $before  = $matches[2];
            $classes = $matches[3];
            $after   = $matches[4];

            $styles    = [];
            $remaining = [];

            foreach (preg_split('/\s+/', trim($classes)) as $cls) {
               if (isset($classToStyle[$cls])) {
                  $styles[] = $classToStyle[$cls];
               } else {
                  $remaining[] = $cls;
               }
            }

            if (empty($styles)) return $matches[0];

            $inlineStyle = implode(' ', $styles);
            $fullAttrs = $before . $after;

            if (preg_match('/style\s*=\s*"([^"]*)"/i', $fullAttrs)) {
               $result = "<{$tag}{$before}";
               if (!empty($remaining)) {
                  $result .= 'class="' . implode(' ', $remaining) . '" ';
               }
               $result .= preg_replace(
                  '/style\s*=\s*"([^"]*)"/i',
                  'style="$1 ' . $inlineStyle . '"',
                  $after
               );
               $result .= '>';
            } else {
               $result = "<{$tag}{$before}";
               if (!empty($remaining)) {
                  $result .= 'class="' . implode(' ', $remaining) . '" ';
               }
               $result .= 'style="' . $inlineStyle . '"' . $after . '>';
            }

            return $result;
         },
         $html
      );

      return $html;
   }

   /**
    * Genera el PDF de un documento ya generado (para re-descarga/preview).
    */
   public function streamPdf(DocumentoGenerado $documento)
   {
      $ruta = $documento->ruta_archivo_pdf;

      if ($ruta && Storage::disk('local')->exists($ruta)) {
         return Storage::disk('local')->response($ruta, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
         ]);
      }

      // Fallback: regenerar desde contenido_generado
      $plantilla = $documento->plantillaUtilizada;

      $pdf = Pdf::loadView('hr::contratos.documento-generado', [
         'plantilla'       => $plantilla,
         'contenido'       => $documento->contenido_generado,
         'seccionesInicio' => collect(),
         'seccionesCuerpo' => collect(),
         'seccionesFinal'  => collect(),
         'numeroDocumento' => $documento->numero_documento,
      ]);

      if ($plantilla) {
         $pdf->setPaper(
            strtolower($plantilla->formato_papel ?? 'a4'),
            $plantilla->orientacion === 'horizontal' ? 'landscape' : 'portrait'
         );
      }

      $pdf->setOption('margin_top', 72);
      $pdf->setOption('margin_bottom', 60);
      $pdf->setOption('margin_left', 72);
      $pdf->setOption('margin_right', 56);

      return $pdf->stream("documento-{$documento->numero_documento}.pdf");
   }
}
