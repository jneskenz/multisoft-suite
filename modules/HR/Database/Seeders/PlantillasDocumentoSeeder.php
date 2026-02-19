<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillasDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================
        // 1. TIPOS DE DOCUMENTO
        // ============================================
        $tiposDocumento = [
            // CONTRACTUALES
            [
                'codigo' => 'CONT-INDEF',
                'nombre' => 'Contrato Indefinido',
                'categoria' => 'contractual',
                'descripcion' => 'Contrato de trabajo a plazo indeterminado',
                'requiere_firma_empleado' => true,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'CONT-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 1,
            ],
            [
                'codigo' => 'CONT-TEMP',
                'nombre' => 'Contrato Temporal',
                'categoria' => 'contractual',
                'descripcion' => 'Contrato sujeto a modalidad - Temporal',
                'requiere_firma_empleado' => true,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'CONT-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 2,
            ],
            [
                'codigo' => 'CONT-PRAC',
                'nombre' => 'Prácticas Pre-profesionales',
                'categoria' => 'contractual',
                'descripcion' => 'Convenio de prácticas pre-profesionales',
                'requiere_firma_empleado' => true,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'CONV-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 3,
            ],

            // CERTIFICACIONES
            [
                'codigo' => 'CERT-TRAB',
                'nombre' => 'Certificado de Trabajo',
                'categoria' => 'certificacion',
                'descripcion' => 'Certificado que acredita tiempo de servicio',
                'requiere_firma_empleado' => false,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'CERT-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 4,
            ],
            [
                'codigo' => 'CONST-LAB',
                'nombre' => 'Constancia Laboral',
                'categoria' => 'certificacion',
                'descripcion' => 'Constancia de relación laboral vigente',
                'requiere_firma_empleado' => false,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'CONST-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 5,
            ],

            // ADMINISTRATIVOS
            [
                'codigo' => 'MEMO-INT',
                'nombre' => 'Memorandum Interno',
                'categoria' => 'administrativo',
                'descripcion' => 'Comunicación interna formal',
                'requiere_firma_empleado' => true,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'MEMO-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 6,
            ],

            // DISCIPLINARIOS
            [
                'codigo' => 'AMON-DISC',
                'nombre' => 'Carta de Amonestación',
                'categoria' => 'disciplinario',
                'descripcion' => 'Amonestación por falta disciplinaria',
                'requiere_firma_empleado' => true,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => true,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'AMON-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 7,
            ],

            // LIQUIDACIONES
            [
                'codigo' => 'LIQUID-CTS',
                'nombre' => 'Liquidación de CTS',
                'categoria' => 'liquidacion',
                'descripcion' => 'Liquidación de compensación por tiempo de servicios',
                'requiere_firma_empleado' => true,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'LIQ-CTS-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 8,
            ],
            [
                'codigo' => 'LIQUID-FIN',
                'nombre' => 'Liquidación Final',
                'categoria' => 'liquidacion',
                'descripcion' => 'Liquidación de beneficios sociales al cese',
                'requiere_firma_empleado' => true,
                'requiere_firma_empleador' => true,
                'requiere_testigos' => false,
                'requiere_notarizacion' => false,
                'usa_numeracion_automatica' => true,
                'prefijo_numeracion' => 'LIQ-FIN-',
                'formato_numeracion' => '{prefijo}{año}-{numero:4}',
                'estado' => '1',
                'orden' => 9,
            ],
        ];

        $timestamp = now();

        $tiposDocumentoUpsert = array_map(
            fn(array $tipo): array => array_merge($tipo, [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ]),
            $tiposDocumento
        );

        DB::table('hr_tipos_documento')->upsert(
            $tiposDocumentoUpsert,
            ['codigo'],
            [
                'nombre',
                'categoria',
                'descripcion',
                'requiere_firma_empleado',
                'requiere_firma_empleador',
                'requiere_testigos',
                'requiere_notarizacion',
                'usa_numeracion_automatica',
                'prefijo_numeracion',
                'formato_numeracion',
                'estado',
                'orden',
                'updated_at',
                'deleted_at',
            ]
        );

        // Obtener IDs de tipos creados/actualizados
        $tiposPorCodigo = DB::table('hr_tipos_documento')
            ->whereIn('codigo', ['CONT-INDEF', 'CONT-TEMP', 'MEMO-INT', 'AMON-DISC'])
            ->pluck('id', 'codigo');

        if ($tiposPorCodigo->count() !== 4) {
            throw new \RuntimeException('No se pudieron resolver los tipos de documento necesarios para el seeder.');
        }

        $tipoContIndefId = $tiposPorCodigo['CONT-INDEF'];
        $tipoContTempId = $tiposPorCodigo['CONT-TEMP'];
        $tipoMemoId = $tiposPorCodigo['MEMO-INT'];
        $tipoAmonId = $tiposPorCodigo['AMON-DISC'];

        // ============================================
        // 2. CATEGORÍAS DE DOCUMENTO
        // ============================================
        $categorias = [
            // Categorías para Contratos Temporales
            [
                'tipo_documento_id' => $tipoContTempId,
                'codigo' => 'TEMP-NEC-MER',
                'nombre' => 'Necesidades del Mercado',
                'descripcion' => 'Incremento coyuntural de la producción',
                'requiere_justificacion' => true,
                'requiere_aprobacion' => true,
                'nivel_aprobacion' => 'gerencia',
                'articulo_ley' => 'Art. 57 D.S. 003-97-TR',
                'orden' => 1,
            ],
            [
                'tipo_documento_id' => $tipoContTempId,
                'codigo' => 'TEMP-SUPL',
                'nombre' => 'Suplencia',
                'descripcion' => 'Sustitución de trabajador permanente',
                'requiere_justificacion' => true,
                'requiere_aprobacion' => false,
                'nivel_aprobacion' => 'rrhh',
                'articulo_ley' => 'Art. 61 D.S. 003-97-TR',
                'orden' => 2,
            ],

            // Categorías para Memorandums
            [
                'tipo_documento_id' => $tipoMemoId,
                'codigo' => 'MEMO-LLAMADO',
                'nombre' => 'Llamado de Atención',
                'descripcion' => 'Llamado de atención verbal formalizado',
                'requiere_justificacion' => true,
                'requiere_aprobacion' => false,
                'nivel_aprobacion' => 'jefe_directo',
                'orden' => 1,
            ],
            [
                'tipo_documento_id' => $tipoMemoId,
                'codigo' => 'MEMO-FELICIT',
                'nombre' => 'Felicitación',
                'descripcion' => 'Reconocimiento por desempeño destacado',
                'requiere_justificacion' => false,
                'requiere_aprobacion' => false,
                'orden' => 2,
            ],

            // Categorías para Amonestaciones
            [
                'tipo_documento_id' => $tipoAmonId,
                'codigo' => 'AMON-TARD',
                'nombre' => 'Tardanzas Reiteradas',
                'descripcion' => 'Amonestación por tardanzas repetitivas',
                'requiere_justificacion' => true,
                'requiere_aprobacion' => true,
                'nivel_aprobacion' => 'rrhh',
                'articulo_ley' => 'Art. 25 D.S. 003-97-TR',
                'orden' => 1,
            ],
            [
                'tipo_documento_id' => $tipoAmonId,
                'codigo' => 'AMON-FALTA',
                'nombre' => 'Falta Grave',
                'descripcion' => 'Amonestación por falta grave',
                'requiere_justificacion' => true,
                'requiere_aprobacion' => true,
                'nivel_aprobacion' => 'gerencia',
                'articulo_ley' => 'Art. 25 D.S. 003-97-TR',
                'orden' => 2,
            ],
        ];

        $categoriasUpsert = array_map(
            fn(array $categoria): array => array_merge([
                'nivel_aprobacion' => null,
                'articulo_ley' => null,
            ], $categoria, [
                'estado' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ]),
            $categorias
        );

        DB::table('hr_categorias_documento')->upsert(
            $categoriasUpsert,
            ['codigo'],
            [
                'tipo_documento_id',
                'nombre',
                'descripcion',
                'requiere_justificacion',
                'requiere_aprobacion',
                'nivel_aprobacion',
                'articulo_ley',
                'estado',
                'orden',
                'updated_at',
                'deleted_at',
            ]
        );

        // ============================================
        // 3. SECCIONES REUTILIZABLES
        // ============================================
        $secciones = [
            [
                'codigo' => 'SEC-ENC-EMPRESA',
                'nombre' => 'Datos del Empleador',
                'descripcion' => 'Información legal del empleador',
                'contenido_html' => '<p><strong>{{empresa.razon_social}}</strong>, con R.U.C. N° {{empresa.ruc}}, con domicilio en {{empresa.direccion}}, debidamente representada por su Gerente General <strong>{{empresa.representante_legal}}</strong>, identificado con D.N.I. N° {{empresa.representante_dni}}, a quien en adelante se le denominará <strong>EL EMPLEADOR</strong>.</p>',
                'categoria' => 'encabezado',
                'aplicable_a' => json_encode(['CONT-INDEF', 'CONT-TEMP', 'CONT-PRAC', 'MEMO-INT', 'AMON-DISC']),
                'es_obligatoria' => true,
                'variables_usadas' => json_encode(['empresa.razon_social', 'empresa.ruc', 'empresa.direccion', 'empresa.representante_legal', 'empresa.representante_dni']),
                'orden' => 1,
            ],
            [
                'codigo' => 'SEC-ENC-EMPLEADO',
                'nombre' => 'Datos del Trabajador',
                'descripcion' => 'Información del empleado',
                'contenido_html' => '<p>De la otra parte, <strong>{{empleado.nombre_completo}}</strong>, identificado con {{empleado.documento_tipo}} N° {{empleado.documento_numero}}, con domicilio en {{empleado.direccion}}, a quien en adelante se le denominará <strong>EL TRABAJADOR</strong>.</p>',
                'categoria' => 'encabezado',
                'aplicable_a' => json_encode(['CONT-INDEF', 'CONT-TEMP', 'CONT-PRAC']),
                'es_obligatoria' => true,
                'variables_usadas' => json_encode(['empleado.nombre_completo', 'empleado.documento_tipo', 'empleado.documento_numero', 'empleado.direccion']),
                'orden' => 2,
            ],
            [
                'codigo' => 'SEC-CONF',
                'nombre' => 'Confidencialidad',
                'descripcion' => 'Cláusula de confidencialidad',
                'contenido_html' => '<h3>CONFIDENCIALIDAD</h3><p><strong>EL TRABAJADOR</strong> se obliga a mantener absoluta reserva y confidencialidad sobre toda la información, documentación, procesos, metodologías, bases de datos, secretos comerciales y demás información sensible de <strong>EL EMPLEADOR</strong> a la que tenga acceso durante la vigencia de la relación laboral.</p><p>Esta obligación de confidencialidad se mantendrá vigente incluso después de la terminación del contrato por un período de dos (02) años, bajo pena de indemnización por daños y perjuicios.</p>',
                'categoria' => 'clausula',
                'aplicable_a' => json_encode(['CONT-INDEF', 'CONT-TEMP']),
                'es_obligatoria' => false,
                'variables_usadas' => json_encode([]),
                'orden' => 7,
            ],
            [
                'codigo' => 'SEC-PROP-INT',
                'nombre' => 'Propiedad Intelectual',
                'descripcion' => 'Cláusula de cesión de derechos',
                'contenido_html' => '<h3>PROPIEDAD INTELECTUAL</h3><p>Toda creación intelectual, invención, desarrollo, código fuente, diseño, metodología, proceso o cualquier otra obra que <strong>EL TRABAJADOR</strong> desarrolle durante la vigencia del contrato, en el ejercicio de sus funciones o utilizando recursos de <strong>EL EMPLEADOR</strong>, será de propiedad exclusiva de <strong>EL EMPLEADOR</strong>.</p>',
                'categoria' => 'clausula',
                'aplicable_a' => json_encode(['CONT-INDEF', 'CONT-TEMP']),
                'es_obligatoria' => false,
                'variables_usadas' => json_encode([]),
                'orden' => 8,
            ],
            [
                'codigo' => 'SEC-PIE-FIRMA',
                'nombre' => 'Bloque de Firmas',
                'descripcion' => 'Sección de firmas estándar',
                'contenido_html' => '<div style="margin-top: 5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;"><div style="text-align: center;"><p style="border-top: 2px solid #000; padding-top: 0.5rem; margin-top: 3rem;"><strong>EL EMPLEADOR</strong></p><p>{{empresa.representante_legal}}</p><p>{{empresa.razon_social}}</p></div><div style="text-align: center;"><p style="border-top: 2px solid #000; padding-top: 0.5rem; margin-top: 3rem;"><strong>EL TRABAJADOR</strong></p><p>{{empleado.nombre_completo}}</p><p>DNI: {{empleado.documento_numero}}</p></div></div>',
                'categoria' => 'footer',
                'aplicable_a' => json_encode(['CONT-INDEF', 'CONT-TEMP', 'CONT-PRAC', 'MEMO-INT', 'AMON-DISC']),
                'es_obligatoria' => true,
                'variables_usadas' => json_encode(['empresa.representante_legal', 'empresa.razon_social', 'empleado.nombre_completo', 'empleado.documento_numero']),
                'orden' => 99,
            ],
        ];

        $seccionesUpsert = array_map(
            fn(array $seccion): array => array_merge($seccion, [
                'estado' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ]),
            $secciones
        );

        DB::table('hr_plantillas_secciones')->upsert(
            $seccionesUpsert,
            ['codigo'],
            [
                'nombre',
                'descripcion',
                'contenido_html',
                'categoria',
                'aplicable_a',
                'es_obligatoria',
                'variables_usadas',
                'estado',
                'orden',
                'updated_at',
                'deleted_at',
            ]
        );

        // ============================================
        // 4. PLANTILLAS DE DOCUMENTO
        // ============================================
        $plantillas = [
            [
                'codigo' => 'PLT-CONT-INDEF-001',
                'nombre' => 'Plantilla Contrato Indefinido Estándar',
                'descripcion' => 'Plantilla base para contratos a plazo indefinido',
                'tipo_documento_id' => $tipoContIndefId,
                'categoria_documento_id' => null,
                'contenido_html' => $this->getPlantillaContratoIndefinido(),
                'idioma' => 'es',
                'formato_papel' => 'A4',
                'orientacion' => 'vertical',
                'margenes' => json_encode(['top' => 2.5, 'right' => 2.5, 'bottom' => 2.5, 'left' => 2.5]),
                'variables_disponibles' => json_encode($this->getVariablesDisponibles()),
                'version' => '1.0',
                'es_predeterminada' => true,
                'permite_anexos' => true,
                'anexos_requeridos' => json_encode(['DNI', 'Certificado médico', 'Antecedentes penales']),
                'estado' => '1',
            ],
        ];

        $plantillasUpsert = array_map(
            fn(array $plantilla): array => array_merge($plantilla, [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ]),
            $plantillas
        );

        DB::table('hr_plantillas_documento')->upsert(
            $plantillasUpsert,
            ['codigo'],
            [
                'nombre',
                'descripcion',
                'tipo_documento_id',
                'categoria_documento_id',
                'contenido_html',
                'idioma',
                'formato_papel',
                'orientacion',
                'margenes',
                'variables_disponibles',
                'version',
                'es_predeterminada',
                'permite_anexos',
                'anexos_requeridos',
                'estado',
                'updated_at',
                'deleted_at',
            ]
        );

        echo "Seeder ejecutado correctamente:\n";
        echo "   - 9 tipos de documento creados/actualizados\n";
        echo "   - 6 categorías de documento creadas/actualizadas\n";
        echo "   - 5 secciones reutilizables creadas/actualizadas\n";
        echo "   - 1 plantilla de documento creada/actualizada\n";
    }

    /**
     * Retorna el HTML de la plantilla de contrato indefinido
     */
    private function getPlantillaContratoIndefinido(): string
    {
        return '<h1 style="text-align: center;">CONTRATO DE TRABAJO A PLAZO INDEFINIDO</h1>
<p style="text-align: center;">Contrato N.° {{documento.numero}}</p>
<p style="text-align: center;">--------------------------------------------------</p>
<p>Conste por el presente documento el <strong>CONTRATO DE TRABAJO A PLAZO INDEFINIDO</strong> que celebran:</p>

<p><strong>EL EMPLEADOR:</strong> {{empresa.razon_social}}, con R.U.C. N° {{empresa.ruc}}, representada por {{empresa.representante_legal}}.</p>

<p><strong>EL TRABAJADOR:</strong> {{empleado.nombre_completo}}, identificado con {{empleado.documento_tipo}} N° {{empleado.documento_numero}}.</p>

<h3>PRIMERA: OBJETO DEL CONTRATO</h3>
<p>EL EMPLEADOR contrata los servicios de EL TRABAJADOR para desempeñar el cargo de <strong>{{cargo.nombre}}</strong> en el departamento de {{cargo.departamento}}.</p>

<h3>SEGUNDA: PLAZO Y VIGENCIA</h3>
<p>El presente contrato es de naturaleza <strong>INDETERMINADA</strong> e inicia su vigencia a partir del día {{contrato.fecha_inicio}}.</p>
<p>El contrato se encuentra sujeto a un <strong>PERÍODO DE PRUEBA</strong> de tres (03) meses.</p>

<h3>TERCERA: REMUNERACIÓN</h3>
<p>La remuneración mensual será de {{contrato.moneda}} {{contrato.salario_base}}.</p>

<h3>CUARTA: JORNADA LABORAL</h3>
<p>La jornada será de 48 horas semanales, de lunes a viernes, en el horario: {{contrato.horario}}.</p>

<p style="margin-top: 3rem;">Lima, {{documento.fecha_generacion}}</p>

<div style="display: flex; justify-content: space-between; margin-top: 5rem;">
    <div style="text-align: center;">
        <p>_______________________</p>
        <p><strong>EL EMPLEADOR</strong></p>
        <p>{{empresa.representante_legal}}</p>
    </div>
    <div style="text-align: center;">
        <p>_______________________</p>
        <p><strong>EL TRABAJADOR</strong></p>
        <p>{{empleado.nombre_completo}}</p>
    </div>
</div>';
    }

    /**
     * Retorna las variables disponibles para las plantillas
     */
    private function getVariablesDisponibles(): array
    {
        return [
            'empleado' => [
                'nombres',
                'apellidos',
                'nombre_completo',
                'documento_tipo',
                'documento_numero',
                'fecha_nacimiento',
                'direccion',
                'email',
                'telefono'
            ],
            'contrato' => [
                'numero',
                'fecha_inicio',
                'fecha_fin',
                'salario_base',
                'moneda',
                'horario'
            ],
            'cargo' => [
                'nombre',
                'nivel',
                'departamento'
            ],
            'empresa' => [
                'razon_social',
                'ruc',
                'representante_legal',
                'representante_dni',
                'direccion'
            ],
            'documento' => [
                'numero',
                'fecha_generacion'
            ]
        ];
    }
}

