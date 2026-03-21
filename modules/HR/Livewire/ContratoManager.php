<?php

namespace Modules\HR\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Models\GroupCompany;
use Modules\HR\Enums\EstadoContrato;
use Modules\HR\Models\Cargo;
use Modules\HR\Models\CategoriaDocumento;
use Modules\HR\Models\Contrato;
use Modules\HR\Models\Empleado;
use Modules\HR\Models\PlantillaDocumento;
use Modules\HR\Models\TipoDocumento;
use Modules\HR\Services\DocumentoService;

class ContratoManager extends Component
{
   use WithPagination;

   // ─── Búsqueda y filtros ───────────────────────────────────
   #[Url(as: 'q')]
   public string $search = '';
   #[Url]
   public string $tipoFilter = '';
   #[Url]
   public string $estadoFilter = '';

   public string $sortField = 'created_at';
   public string $sortDirection = 'desc';
   public int $perPage = 10;

   // ─── Formulario ───────────────────────────────────────────
   public bool $showModal = false;
   public bool $showDeleteModal = false;
   public bool $showTerminarModal = false;
   public bool $isEditing = false;
   public ?int $contratoId = null;

   public ?int $empleado_id = null;
   public ?int $cargo_id = null;
   public string $numero_contrato = '';
   public ?int $tipo_contrato_id = null;
   public ?int $modalidad_id = null;
   public string $fecha_inicio = '';
   public string $fecha_fin = '';
   public string $salario_base = '';
   public int $estado = 1;
   public ?int $estado_contrato = 0;
   public string $horas_semanales = '';
   public string $descripcion_horario = '';
   public string $notas = '';

   // Terminar contrato
   public string $motivo_terminacion = '';
   public string $fecha_terminacion = '';

   // SoftDelete
   public bool $showRestoreModal = false;
   public bool $showForceDeleteModal = false;
   public bool $showTrashedContratos = false;

   // Generar documento
   public bool $showGenerarDocModal = false;
   public ?int $plantilla_seleccionada_id = null;
   public ?int $generarDocContratoId = null;

   // ─── Propiedades computadas ───────────────────────────────
   #[Computed]
   public function currentGroup(): ?GroupCompany
   {
      return current_group();
   }

   #[Computed]
   public function contratos()
   {
      $group = $this->currentGroup;

      $query = Contrato::query()
         ->with(['empleado.company', 'tipoContrato', 'modalidad', 'cargo', 'documentosGenerados'])
         ->whereHas('empleado', function ($q) use ($group) {
            if ($group) {
               $q->where('group_company_id', $group->id);
            }
         })
         ->when($this->search, fn($q) => $q->where(function ($q) {
            $q->where('numero_contrato', 'like', "%{$this->search}%")
               ->orWhereHas(
                  'empleado',
                  fn($eq) =>
                  $eq->where('nombre', 'like', "%{$this->search}%")
                     ->orWhere('documento_numero', 'like', "%{$this->search}%")
                     ->orWhere('codigo_empleado', 'like', "%{$this->search}%")
               );
         }))
         ->when(
            $this->tipoFilter !== '',
            fn($q) =>
            $q->where('tipo_contrato_id', (int) $this->tipoFilter)
         )
         ->when($this->estadoFilter !== '', function ($q) {
            if ($this->estadoFilter === 'vigente') {
               $q->vigente();
            } elseif ($this->estadoFilter === 'vencido') {
               $q->vencido();
            } elseif ($this->estadoFilter === 'por_vencer') {
               $q->porVencer();
            } elseif (is_numeric($this->estadoFilter)) {
               $q->where('estado_contrato', (int) $this->estadoFilter);
            }
         })
         ->orderBy($this->sortField, $this->sortDirection);

      if ($this->showTrashedContratos) {
         $query->onlyTrashed();
      }

      return $query->paginate($this->perPage);
   }

   #[Computed]
   public function empleados()
   {
      $group = $this->currentGroup;

      // IDs de empleados que ya tienen contrato no eliminado
      $empleadosConContrato = Contrato::whereNull('deleted_at')
         ->when($this->isEditing && $this->contratoId, fn($q) => $q->where('id', '!=', $this->contratoId))
         ->pluck('empleado_id')
         ->toArray();

      return Empleado::query()
         ->when($group, fn($q) => $q->where('group_company_id', $group->id))
         ->where('estado', Empleado::ESTADO_ACTIVO)
         ->orderBy('nombres')
         ->get(['id', 'nombres', 'apellidos', 'codigo_empleado', 'documento_numero'])
         ->map(fn($emp) => $emp->setAttribute('tiene_contrato', in_array($emp->id, $empleadosConContrato)));
   }

   #[Computed]
   public function cargos()
   {
      return Cargo::active()
         ->orderBy('nombre')
         ->get(['id', 'nombre', 'codigo']);
   }

   /**
    * Tipos de documento contractuales (para el dropdown de tipo de contrato).
    */
   #[Computed]
   public function tiposContrato()
   {
      return TipoDocumento::where('categoria', 'contractual')
         ->active()
         ->orderBy('orden')
         ->get(['id', 'codigo', 'nombre']);
   }

   /**
    * Modalidades/categorías del tipo de contrato seleccionado.
    */
   #[Computed]
   public function modalidades()
   {
      if (!$this->tipo_contrato_id) {
         return collect();
      }
      return CategoriaDocumento::where('tipo_documento_id', $this->tipo_contrato_id)
         ->active()
         ->orderBy('orden')
         ->get(['id', 'codigo', 'nombre']);
   }

   /**
    * Plantillas disponibles para el tipo del contrato seleccionado (para modal de generación).
    */
   #[Computed]
   public function plantillasDisponibles()
   {
      if (!$this->generarDocContratoId) {
         return collect();
      }
      $contrato = Contrato::find($this->generarDocContratoId);
      if (!$contrato || !$contrato->tipo_contrato_id) {
         return collect();
      }

      return PlantillaDocumento::where('tipo_documento_id', $contrato->tipo_contrato_id)
         ->when($contrato->modalidad_id, fn($q) => $q->where(function ($q2) use ($contrato) {
            $q2->where('categoria_documento_id', $contrato->modalidad_id)
               ->orWhereNull('categoria_documento_id');
         }))
         ->where('estado', '1')
         ->orderBy('es_predeterminada', 'desc')
         ->orderBy('nombre')
         ->get(['id', 'codigo', 'nombre', 'es_predeterminada']);
   }

   // ─── Validación ───────────────────────────────────────────
   protected function rules(): array
   {
      return [
         'empleado_id'       => ['required', 'exists:hr_empleados,id'],
         'cargo_id'          => ['required', 'exists:hr_cargos,id'],
         'numero_contrato'   => [
            'required',
            'string',
            'max:50',
            Rule::unique('hr_contratos', 'numero_contrato')->ignore($this->contratoId)
         ],
         'tipo_contrato_id'  => ['required', 'exists:hr_tipos_documento,id'],
         'modalidad_id'      => ['nullable', 'exists:hr_categorias_documento,id'],
         'fecha_inicio'      => ['required', 'date'],
         'fecha_fin'         => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
         'salario_base'      => ['nullable', 'numeric', 'min:0'],
         'horas_semanales'   => ['nullable', 'numeric', 'min:0', 'max:168'],
         'descripcion_horario' => ['nullable', 'string', 'max:500'],
         'notas'             => ['nullable', 'string', 'max:2000'],
      ];
   }

   protected function messages(): array
   {
      return [
         'empleado_id.required'      => 'Debe seleccionar un empleado.',
         'empleado_id.exists'        => 'El empleado seleccionado no existe.',
         'cargo_id.required'         => 'Debe seleccionar un cargo.',
         'cargo_id.exists'           => 'El cargo seleccionado no existe.',
         'numero_contrato.required'  => 'El número de contrato es obligatorio.',
         'numero_contrato.unique'    => 'Este número de contrato ya existe.',
         'tipo_contrato_id.required' => 'El tipo de contrato es obligatorio.',
         'tipo_contrato_id.exists'   => 'Seleccione un tipo de contrato válido.',
         'fecha_inicio.required'     => 'La fecha de inicio es obligatoria.',
         'fecha_fin.after_or_equal'  => 'La fecha de fin debe ser posterior a la de inicio.',
         'salario_base.numeric'      => 'El salario debe ser un número.',
         'horas_semanales.numeric'   => 'Las horas semanales deben ser un número.',
      ];
   }

   // ─── Cuando cambia el tipo de contrato, resetear modalidad ─
   public function updatedTipoContratoId(): void
   {
      $this->modalidad_id = null;
      unset($this->modalidades); // refresh computed
   }

   // ─── Ordenamiento y filtros ───────────────────────────────
   public function sortBy(string $field): void
   {
      if ($this->sortField === $field) {
         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
         $this->sortField = $field;
         $this->sortDirection = 'asc';
      }
   }

   public function updatedSearch(): void
   {
      $this->resetPage();
   }

   public function clearFilters(): void
   {
      $this->reset(['search', 'tipoFilter', 'estadoFilter']);
      $this->resetPage();
   }

   // ─── CRUD - Crear ─────────────────────────────────────────
   #[On('openCreateModal')]
   public function create(): void
   {
      $this->resetForm();
      $this->isEditing = false;
      $this->showModal = true;

      // Generar número de contrato automático
      $this->numero_contrato = $this->generateNumeroContrato();
   }

   /**
    * Crear contrato pre-cargando un empleado (desde el listado de empleados).
    */
   #[On('createContratoForEmpleado')]
   public function createForEmpleado(int $empleadoId): void
   {
      $this->resetForm();
      $this->isEditing = false;
      $this->empleado_id = $empleadoId;
      $this->numero_contrato = $this->generateNumeroContrato();
      $this->showModal = true;
   }

   // ─── CRUD - Editar ────────────────────────────────────────
   public function edit(int $id): void
   {
      $contrato = Contrato::findOrFail($id);

      $this->contratoId = $contrato->id;
      $this->empleado_id = $contrato->empleado_id;
      $this->cargo_id = $contrato->cargo_id;
      $this->numero_contrato = $contrato->numero_contrato;
      $this->tipo_contrato_id = $contrato->tipo_contrato_id;
      $this->modalidad_id = $contrato->modalidad_id;
      $this->fecha_inicio = $contrato->fecha_inicio?->format('Y-m-d') ?? '';
      $this->fecha_fin = $contrato->fecha_fin?->format('Y-m-d') ?? '';
      $this->salario_base = $contrato->salario_base ?? '';
      $this->estado = $contrato->estado;
      $this->estado_contrato = $contrato->estado_contrato?->value ?? 0;
      $this->horas_semanales = $contrato->horas_semanales ?? '';
      $this->descripcion_horario = $contrato->descripcion_horario ?? '';
      $this->notas = $contrato->notas ?? '';

      $this->isEditing = true;
      $this->showModal = true;
   }

   // ─── CRUD - Guardar ───────────────────────────────────────
   public function save(): void
   {
      $validated = $this->validate();

      $data = [
         'empleado_id'         => $this->empleado_id,
         'cargo_id'            => $this->cargo_id,
         'numero_contrato'     => $this->numero_contrato,
         'tipo_contrato_id'    => $this->tipo_contrato_id,
         'modalidad_id'        => $this->modalidad_id ?: null,
         'fecha_inicio'        => $this->fecha_inicio,
         'fecha_fin'           => $this->fecha_fin ?: null,
         'salario_base'        => $this->salario_base ?: null,
         'horas_semanales'     => $this->horas_semanales ?: null,
         'descripcion_horario' => $this->descripcion_horario ?: null,
         'notas'               => $this->notas ?: null,
         'estado'              => 1,
         'updated_by'          => auth()->id(),
      ];

      DB::transaction(function () use ($data, &$message) {
         if ($this->isEditing) {
            $contrato = Contrato::findOrFail($this->contratoId);
            $contrato->update($data);

            // Regenerar documento borrador si sigue en BORRADOR
            if ($contrato->estado_contrato === EstadoContrato::BORRADOR) {
               $this->regenerarDocumentoBorrador($contrato);
            }

            $message = __('Contrato actualizado exitosamente.');
         } else {
            $data['estado_contrato'] = EstadoContrato::BORRADOR->value;
            $data['created_by'] = auth()->id();
            $contrato = Contrato::create($data);

            // Auto-generar documento borrador
            $this->generarDocumentoBorrador($contrato);

            $message = __('Contrato creado exitosamente.');
         }
      });

      $this->closeModal();
      $this->dispatch('notify', type: 'success', message: $message ?? '');
   }

   /**
    * Genera el documento borrador automáticamente al crear un contrato.
    */
   private function generarDocumentoBorrador(Contrato $contrato): void
   {
      // Buscar plantilla predeterminada para el tipo de contrato
      $plantilla = PlantillaDocumento::where('tipo_documento_id', $contrato->tipo_contrato_id)
         ->when($contrato->modalidad_id, fn($q) => $q->where(function ($q2) use ($contrato) {
            $q2->where('categoria_documento_id', $contrato->modalidad_id)
               ->orWhereNull('categoria_documento_id');
         }))
         ->where('estado', '1')
         ->orderBy('es_predeterminada', 'desc')
         ->first();

      if (!$plantilla) {
         return; // Sin plantilla disponible, no se genera documento
      }

      $service = app(DocumentoService::class);
      $service->generarDesdeContrato($contrato, $plantilla, '0'); // estado_documento = 0 (Borrador)
   }

   /**
    * Regenera el documento cuando el contrato está en BORRADOR y se edita.
    */
   private function regenerarDocumentoBorrador(Contrato $contrato): void
   {
      // Eliminar documentos borrador anteriores
      $contrato->documentosGenerados()
         ->where('estado_documento', '0')
         ->each(function ($doc) {
            if ($doc->ruta_archivo_pdf) {
               \Illuminate\Support\Facades\Storage::disk('local')->delete($doc->ruta_archivo_pdf);
            }
            $doc->delete();
         });

      $this->generarDocumentoBorrador($contrato);
   }

   // ─── CRUD - Eliminar ──────────────────────────────────────
   public function confirmDelete(int $id): void
   {
      $this->contratoId = $id;
      $this->showDeleteModal = true;
   }

   public function delete(): void
   {
      Contrato::findOrFail($this->contratoId)->delete();
      session()->flash('message', __('Contrato eliminado.'));
      $this->closeModal();
   }

   // ─── Terminar contrato ────────────────────────────────────
   public function confirmTerminar(int $id): void
   {
      $this->contratoId = $id;
      $this->fecha_terminacion = now()->format('Y-m-d');
      $this->motivo_terminacion = '';
      $this->showTerminarModal = true;
   }

   public function terminar(): void
   {
      $this->validate([
         'motivo_terminacion' => 'required|string|max:100',
         'fecha_terminacion' => 'required|date',
      ], [
         'motivo_terminacion.required' => 'El motivo de terminación es obligatorio.',
         'fecha_terminacion.required' => 'La fecha de terminación es obligatoria.',
      ]);

      $contrato = Contrato::findOrFail($this->contratoId);
      $contrato->update([
         'estado_contrato'    => EstadoContrato::TERMINADO,
         'motivo_terminacion' => $this->motivo_terminacion,
         'fecha_terminacion'  => $this->fecha_terminacion,
         'updated_by'         => auth()->id(),
      ]);

      session()->flash('message', __('Contrato terminado exitosamente.'));
      $this->closeModal();
   }

   // ─── Flujo de estados ──────────────────────────────────────
   public function confirmarContrato(int $id): void
   {
      $contrato = Contrato::findOrFail($id);

      if ($contrato->estado_contrato !== EstadoContrato::BORRADOR) {
         $this->dispatch('notify', type: 'error', message: __('Solo se pueden confirmar contratos en estado Borrador.'));
         return;
      }

      DB::transaction(function () use ($contrato) {
         $contrato->update([
            'estado_contrato' => EstadoContrato::REVISION,
            'updated_by' => auth()->id(),
         ]);

         // Actualizar documentos generados a "Por firmar"
         $contrato->documentosGenerados()
            ->where('estado_documento', '0')
            ->update(['estado_documento' => '1', 'actualizado_por' => auth()->id()]);
      });

      $this->dispatch('notify', type: 'success', message: __('Contrato enviado a revisión / firma.'));
   }

   public function firmarContrato(int $id): void
   {
      $contrato = Contrato::findOrFail($id);

      if ($contrato->estado_contrato !== EstadoContrato::REVISION) {
         $this->dispatch('notify', type: 'error', message: __('Solo se pueden firmar contratos en estado Revisión.'));
         return;
      }

      DB::transaction(function () use ($contrato) {
         $contrato->update([
            'estado_contrato' => EstadoContrato::FIRMADO,
            'updated_by' => auth()->id(),
         ]);

         // Actualizar documentos a "Vigente"
         $contrato->documentosGenerados()
            ->where('estado_documento', '1')
            ->update([
               'estado_documento' => '2',
               'estado_firmas' => 'firmado',
               'firmado_por_empleador_at' => now(),
               'actualizado_por' => auth()->id(),
            ]);
      });

      $this->dispatch('notify', type: 'success', message: __('Contrato firmado exitosamente.'));
   }

   public function rechazarContrato(int $id): void
   {
      $contrato = Contrato::findOrFail($id);

      if ($contrato->estado_contrato !== EstadoContrato::REVISION) {
         $this->dispatch('notify', type: 'error', message: __('Solo se pueden rechazar contratos en estado Revisión.'));
         return;
      }

      DB::transaction(function () use ($contrato) {
         $contrato->update([
            'estado_contrato' => EstadoContrato::RECHAZADO,
            'updated_by' => auth()->id(),
         ]);

         $contrato->documentosGenerados()
            ->where('estado_documento', '1')
            ->update([
               'estado_documento' => '5',
               'estado_firmas' => 'rechazado',
               'actualizado_por' => auth()->id(),
            ]);
      });

      $this->dispatch('notify', type: 'error', message: __('Contrato rechazado.'));
   }

   // ─── Helpers ──────────────────────────────────────────────
   public function closeModal(): void
   {
      $this->showModal = false;
      $this->showDeleteModal = false;
      $this->showTerminarModal = false;
      $this->showRestoreModal = false;
      $this->showForceDeleteModal = false;
      $this->showGenerarDocModal = false;
      $this->resetForm();
   }

   private function resetForm(): void
   {
      $this->reset([
         'contratoId',
         'empleado_id',
         'cargo_id',
         'numero_contrato',
         'tipo_contrato_id',
         'modalidad_id',
         'fecha_inicio',
         'fecha_fin',
         'salario_base',
         'estado_contrato',
         'horas_semanales',
         'descripcion_horario',
         'notas',
         'motivo_terminacion',
         'fecha_terminacion',
         'plantilla_seleccionada_id',
         'generarDocContratoId',
      ]);
      $this->estado = 1;
      $this->estado_contrato = 0;
      $this->resetValidation();
   }

   private function generateNumeroContrato(): string
   {
      $year = now()->year;
      $last = Contrato::withTrashed()
         ->where('numero_contrato', 'like', "CTR-{$year}-%")
         ->orderByDesc('numero_contrato')
         ->value('numero_contrato');

      if ($last) {
         $seq = (int) substr($last, -4) + 1;
      } else {
         $seq = 1;
      }

      return sprintf('CTR-%d-%04d', $year, $seq);
   }

   // ─── SoftDeletes ──────────────────────────────────────────
   public function toggleTrashedContratos(): void
   {
      $this->showTrashedContratos = !$this->showTrashedContratos;
      $this->resetPage();
   }

   public function confirmRestore(int $id): void
   {
      $this->contratoId = $id;
      $this->showRestoreModal = true;
   }

   public function restore(): void
   {
      Contrato::withTrashed()->findOrFail($this->contratoId)->restore();
      session()->flash('message', __('Contrato restaurado.'));
      $this->closeModal();
   }

   public function confirmForceDelete(int $id): void
   {
      $this->contratoId = $id;
      $this->showForceDeleteModal = true;
   }

   public function forceDelete(): void
   {
      Contrato::withTrashed()->findOrFail($this->contratoId)->forceDelete();
      session()->flash('message', __('Contrato eliminado permanentemente.'));
      $this->closeModal();
   }

   // ─── Generar documento ──────────────────────────────────────
   public function openGenerarDocModal(int $contratoId): void
   {
      $this->generarDocContratoId = $contratoId;
      $this->plantilla_seleccionada_id = null;
      unset($this->plantillasDisponibles);

      // Seleccionar la plantilla predeterminada automáticamente
      $defaultPlantilla = $this->plantillasDisponibles->firstWhere('es_predeterminada', true);
      if ($defaultPlantilla) {
         $this->plantilla_seleccionada_id = $defaultPlantilla->id;
      }

      $this->showGenerarDocModal = true;
   }

   public function generarDocumento(): void
   {
      $this->validate([
         'plantilla_seleccionada_id' => 'required|exists:hr_plantillas_documento,id',
      ], [
         'plantilla_seleccionada_id.required' => 'Debe seleccionar una plantilla.',
      ]);

      $contrato = Contrato::findOrFail($this->generarDocContratoId);
      $plantilla = PlantillaDocumento::findOrFail($this->plantilla_seleccionada_id);

      $service = app(DocumentoService::class);
      $documento = $service->generarDesdeContrato($contrato, $plantilla);

      $url = group_route('hr.contratos.ver-documento', [
         'documento' => $documento->id,
      ]);

      $this->closeModal();

      session()->flash('message', __('Documento generado exitosamente.'));

      // Emit event to open PDF in new tab from JavaScript
      $this->dispatch('openPdfInNewTab', url: $url);
   }

   // ─── Render ───────────────────────────────────────────────
   public function render()
   {
      return view('hr::livewire.contrato-manager');
   }
}
