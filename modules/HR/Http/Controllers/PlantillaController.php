<?php

namespace Modules\HR\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\HR\Models\PlantillaDocumento;
use Modules\HR\Models\PlantillaSeccionAsignada;

class PlantillaController extends BaseController
{
    /**
     * Listar plantillas.
     */
    public function index()
    {
        return view('hr::plantillas.index');
    }

    /**
     * Mostrar cargo.
     */
    public function show(string $id)
    {
        return view('hr::plantillas.show', compact('id'));
    }

    /**
     * Preview plantilla as PDF (inline in browser).
     */
    public function previewPdf(Request $request)
    {
        $id = $request->query('id');
        abort_unless($id, 404);

        $plantilla = PlantillaDocumento::with('tipoDocumento')->findOrFail($id);

        // Convert Quill classes to inline styles for DomPDF compatibility
        $plantilla->contenido_html = $this->convertQuillToInline($plantilla->contenido_html ?? '');

        // Load assigned sections in order
        $secciones = PlantillaSeccionAsignada::where('plantilla_id', $plantilla->id)
            ->join('hr_plantillas_secciones', 'hr_plantillas_secciones.id', '=', 'seccion_id')
            ->orderBy('hr_plantillas_secciones_asignadas.orden')
            ->get([
                'hr_plantillas_secciones.nombre',
                'hr_plantillas_secciones.contenido_html',
                'hr_plantillas_secciones_asignadas.ubicacion',
            ]);

        // Convert Quill classes in each section + default null ubicacion to 'cuerpo'
        foreach ($secciones as $sec) {
            $sec->contenido_html = $this->convertQuillToInline($sec->contenido_html ?? '');
            $sec->ubicacion = $sec->ubicacion ?: 'cuerpo';
        }

        // Group sections by location
        $seccionesInicio = $secciones->where('ubicacion', 'inicio');
        $seccionesCuerpo = $secciones->where('ubicacion', 'cuerpo');
        $seccionesFinal  = $secciones->where('ubicacion', 'final');

        $pdf = Pdf::loadView('hr::plantillas.pdf-preview', [
            'plantilla'       => $plantilla,
            'seccionesInicio' => $seccionesInicio,
            'seccionesCuerpo' => $seccionesCuerpo,
            'seccionesFinal'  => $seccionesFinal,
        ]);

        $pdf->setPaper(
            strtolower($plantilla->formato_papel ?? 'a4'),
            $plantilla->orientacion === 'horizontal' ? 'landscape' : 'portrait'
        );

        // Set page margins (in points: 72pt = 1 inch ≈ 2.54cm)
        $pdf->setOption('margin_top', 72);
        $pdf->setOption('margin_bottom', 60);
        $pdf->setOption('margin_left', 72);
        $pdf->setOption('margin_right', 56);

        return $pdf->stream("preview-{$plantilla->codigo}.pdf");
    }

    /**
     * Convert Quill CSS classes to inline styles for DomPDF compatibility.
     * Handles single and multi-class attributes robustly.
     */
    private function convertQuillToInline(string $html): string
    {
        if (empty($html)) return $html;

        // Map of Quill class → inline style property
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

        // Add indent classes
        for ($i = 1; $i <= 8; $i++) {
            $classToStyle['ql-indent-' . $i] = 'margin-left: ' . ($i * 24) . 'pt;';
        }

        // Find all tags with class attributes and convert Quill classes to inline styles
        $html = preg_replace_callback(
            '/<([a-z][a-z0-9]*)\b([^>]*?)class\s*=\s*"([^"]*)"([^>]*?)>/i',
            function ($matches) use ($classToStyle) {
                $tag        = $matches[1];
                $before     = $matches[2];
                $classes    = $matches[3];
                $after      = $matches[4];

                $styles   = [];
                $remaining = [];

                foreach (preg_split('/\s+/', trim($classes)) as $cls) {
                    if (isset($classToStyle[$cls])) {
                        $styles[] = $classToStyle[$cls];
                    } else {
                        $remaining[] = $cls;
                    }
                }

                if (empty($styles)) {
                    return $matches[0]; // No Quill classes found, return unchanged
                }

                $inlineStyle = implode(' ', $styles);

                // Check if tag already has a style attribute
                $fullAttrs = $before . $after;
                if (preg_match('/style\s*=\s*"([^"]*)"/i', $fullAttrs)) {
                    // Append to existing style
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
                    // Create new style attribute
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
}
