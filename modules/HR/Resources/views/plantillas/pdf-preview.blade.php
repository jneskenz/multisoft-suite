<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $plantilla->nombre }}</title>
    <style>
        /* ── Page margins (DomPDF) ── */
        @page {
            margin: 70pt 56pt 56pt 70pt;
        }

        /* ── Base ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.65;
            color: #222;
            padding: 20pt;
        }

        /* ── Document header ── */
        .doc-header {
            text-align: center;
            margin-bottom: 20pt;
            padding-bottom: 10pt;
            border-bottom: 2px solid #1a1a2e;
        }
        .doc-header .doc-type {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #666;
            margin-bottom: 4pt;
        }
        .doc-header .doc-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 2pt;
        }
        .doc-header .doc-code {
            font-size: 8pt;
            color: #999;
        }

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            top: 40%;
            left: 10%;
            font-size: 55pt;
            color: rgba(200, 200, 200, 0.12);
            transform: rotate(-35deg);
            font-weight: bold;
            z-index: -1;
            letter-spacing: 8px;
        }

        /* ─────────────────────────────────────────────
           Quill Editor Output - Full CSS Compatibility
           ───────────────────────────────────────────── */

        /* Headings */
        h1 { font-size: 18pt; font-weight: bold; color: #1a1a2e; margin: 10pt 0 6pt; }
        h2 { font-size: 15pt; font-weight: bold; color: #1a1a2e; margin: 9pt 0 5pt; }
        h3 { font-size: 13pt; font-weight: bold; color: #333;    margin: 8pt 0 4pt; }
        h4 { font-size: 11pt; font-weight: bold; color: #333;    margin: 6pt 0 3pt; }

        /* Paragraphs & text */
        p { margin-bottom: 6pt; orphans: 3; widows: 3; }
        strong, b { font-weight: bold; }
        em, i { font-style: italic; }
        u { text-decoration: underline; }
        s, del, strike { text-decoration: line-through; }
        sub { vertical-align: sub; font-size: 0.8em; }
        sup { vertical-align: super; font-size: 0.8em; }
        a { color: #1a73e8; text-decoration: underline; }

        /* Lists */
        ul, ol { margin: 4pt 0 8pt 0; padding-left: 24pt; }
        li { margin-bottom: 3pt; }

        /* Quill indent classes (indent = 3em each) */
        .ql-indent-1 { margin-left: 24pt !important; }
        .ql-indent-2 { margin-left: 48pt !important; }
        .ql-indent-3 { margin-left: 72pt !important; }
        .ql-indent-4 { margin-left: 96pt !important; }
        .ql-indent-5 { margin-left: 120pt !important; }
        .ql-indent-6 { margin-left: 144pt !important; }
        .ql-indent-7 { margin-left: 168pt !important; }
        .ql-indent-8 { margin-left: 192pt !important; }

        /* Quill alignment classes - individual rules for DomPDF */
        .ql-align-center { text-align: center; }
        p.ql-align-center { text-align: center; }
        h1.ql-align-center { text-align: center; }
        h2.ql-align-center { text-align: center; }
        h3.ql-align-center { text-align: center; }
        .ql-align-right { text-align: right; }
        p.ql-align-right { text-align: right; }
        h1.ql-align-right { text-align: right; }
        h2.ql-align-right { text-align: right; }
        h3.ql-align-right { text-align: right; }
        .ql-align-justify { text-align: justify; }
        p.ql-align-justify { text-align: justify; }

        /* Quill font sizes */
        .ql-size-small { font-size: 9pt; }
        .ql-size-large { font-size: 14pt; }
        .ql-size-huge  { font-size: 18pt; }

        /* Quill font families */
        .ql-font-serif { font-family: 'DejaVu Serif', Georgia, 'Times New Roman', serif; }
        .ql-font-monospace { font-family: 'DejaVu Sans Mono', 'Courier New', monospace; }

        /* Quill color & background (inline styles handled natively) */

        /* Blockquote (Quill uses blockquote element) */
        blockquote {
            border-left: 4pt solid #ccc;
            margin: 6pt 0;
            padding: 4pt 12pt;
            color: #555;
            background-color: #f9f9f9;
        }

        /* Code block */
        pre {
            font-family: 'DejaVu Sans Mono', 'Courier New', monospace;
            font-size: 9pt;
            background-color: #f4f4f4;
            padding: 8pt;
            margin: 6pt 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            border: 1pt solid #e0e0e0;
            border-radius: 3pt;
        }
        code {
            font-family: 'DejaVu Sans Mono', 'Courier New', monospace;
            font-size: 9pt;
            background-color: #f4f4f4;
            padding: 1pt 3pt;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin: 6pt 0; }
        td, th { border: 1pt solid #ddd; padding: 5pt 8pt; font-size: 10pt; }
        th { background-color: #f0f0f0; font-weight: bold; }

        /* Images */
        img { max-width: 100%; height: auto; }

        /* HR */
        hr { border: none; border-top: 1pt solid #ccc; margin: 10pt 0; }

        /* ── Section label ── */
        .section-block { margin-bottom: 10pt; }
        .section-label {
            font-size: 8pt;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1pt dashed #ddd;
            padding-bottom: 2pt;
            margin-bottom: 6pt;
        }

        /* ── Content wrapper ── */
        .content-body { margin-bottom: 10pt; }

        /* ── Fixed footer ── */
        .doc-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #bbb;
            padding: 4pt 0;
        }
    </style>
</head>
<body>
    <div class="watermark">VISTA PREVIA</div>

    {{-- Footer fijo --}}
    <div class="doc-footer">
        Vista previa · {{ $plantilla->codigo }} · v{{ $plantilla->version }} · {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- Header --}}
    <div class="doc-header">
        @if ($plantilla->tipoDocumento)
            <div class="doc-type">{{ $plantilla->tipoDocumento->nombre }}</div>
        @endif
        <div class="doc-title">{{ $plantilla->nombre }}</div>
        <div class="doc-code">{{ $plantilla->codigo }} · Versión {{ $plantilla->version }}</div>
    </div>

    {{-- Secciones: Inicio --}}
    @foreach ($seccionesInicio as $sec)
        <div class="section-block">
            <div class="section-label">{{ $sec->nombre }}</div>
            <div class="content-body">{!! $sec->contenido_html !!}</div>
        </div>
    @endforeach

    {{-- Contenido principal --}}
    @if ($plantilla->contenido_html)
        <div class="content-body">{!! $plantilla->contenido_html !!}</div>
    @endif

    {{-- Secciones: Cuerpo --}}
    @foreach ($seccionesCuerpo as $sec)
        <div class="section-block">
            <div class="section-label">{{ $sec->nombre }}</div>
            <div class="content-body">{!! $sec->contenido_html !!}</div>
        </div>
    @endforeach

    {{-- Secciones: Final --}}
    @foreach ($seccionesFinal as $sec)
        <div class="section-block">
            <div class="section-label">{{ $sec->nombre }}</div>
            <div class="content-body">{!! $sec->contenido_html !!}</div>
        </div>
    @endforeach
</body>
</html>
