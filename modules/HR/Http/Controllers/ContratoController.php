<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Modules\HR\Models\Contrato;
use Modules\HR\Models\DocumentoGenerado;
use Modules\HR\Models\PlantillaDocumento;
use Modules\HR\Services\DocumentoService;

class ContratoController extends BaseController
{
   /**
    * Listar contratos.
    */
   public function index()
   {
      return view('hr::contratos.index');
   }

   /**
    * Mostrar detalle de contrato.
    */
   public function show(Request $request)
   {
      $id = (string) $request->route('contrato');

      return view('hr::contratos.show', compact('id'));
   }

   /**
    * Generar documento PDF desde un contrato + plantilla.
    */
   public function generarDocumento(Request $request, DocumentoService $service)
   {
      $contratoId = $request->route('contrato');
      $contrato = Contrato::query()->findOrFail($contratoId);

      $request->validate([
         'plantilla_id' => 'required|exists:hr_plantillas_documento,id',
      ]);

      $plantilla = PlantillaDocumento::findOrFail($request->plantilla_id);

      $documento = $service->generarDesdeContrato($contrato, $plantilla);

      // Retornar JSON con la URL para abrir el PDF
      return response()->json([
         'success' => true,
         'message' => __('Documento generado exitosamente.'),
         'documento_id' => $documento->id,
         'url' => group_route('hr.contratos.ver-documento', [
            'documento' => $documento->id,
         ]),
      ]);
   }

   /**
    * Ver/Descargar PDF de un documento generado.
    */
   public function verDocumento(Request $request, DocumentoService $service)
   {
      $documento = $request->route('documento');
      $doc = DocumentoGenerado::findOrFail($documento);

      return $service->streamPdf($doc);
   }
}
