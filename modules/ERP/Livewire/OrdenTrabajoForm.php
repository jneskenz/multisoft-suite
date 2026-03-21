<?php

namespace Modules\ERP\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\ERP\Models\Catalogo;
use Modules\ERP\Models\OrdenTrabajo;
use Modules\ERP\Models\OrdenTrabajoDetalle;
use Modules\ERP\Models\OrdenTrabajoHistorial;
use Modules\ERP\Models\OrdenTrabajoLente;
use Modules\ERP\Models\Receta;
use Modules\ERP\Models\Ticket;

class OrdenTrabajoForm extends Component
{
    public ?int $ticket_id = null;
    public ?int $receta_id = null;
    public ?int $paciente_id = null;
    public string $fecha_orden = '';
    public string $fecha_prometida = '';
    public string $tipo_orden = 'lentes_completos';
    public string $estado_ot = 'pendiente';
    public string $prioridad = 'normal';
    public string $observaciones = '';
    public string $indicaciones_entrega = '';
    public bool $pacienteBloqueado = false;
    public string $numeroOtPreview = 'OT-PE-000001';
    public array $ticketOptions = [];
    public array $recetaOptions = [];
    public array $patientOptions = [];
    public array $catalogoOptions = [];
    public array $items = [];

    public function mount(): void
    {
        $this->fecha_orden = now()->format('Y-m-d\TH:i');
        $this->cargarOpcionesFormulario();
        $this->agregarItem();
    }

    public function updatedTicketId(mixed $value): void
    {
        $ticketId = (int) $value;

        if ($ticketId <= 0) {
            $this->ticket_id = null;
            $this->resolverPacienteDesdeReferencias();
            return;
        }

        $ticket = collect($this->ticketOptions)->firstWhere('id', $ticketId);
        if (!$ticket) {
            $this->ticket_id = null;
            $this->resolverPacienteDesdeReferencias();
            return;
        }

        $this->ticket_id = $ticketId;

        if ($this->receta_id === null && !empty($ticket['default_receta_id'])) {
            $this->receta_id = (int) $ticket['default_receta_id'];
        }

        $this->paciente_id = (int) ($ticket['patient_id'] ?? 0) ?: null;
        $this->pacienteBloqueado = $this->paciente_id !== null;
    }

    public function updatedRecetaId(mixed $value): void
    {
        $recetaId = (int) $value;

        if ($recetaId <= 0) {
            $this->receta_id = null;
            $this->resolverPacienteDesdeReferencias();
            return;
        }

        $receta = collect($this->recetaOptions)->firstWhere('id', $recetaId);
        if (!$receta) {
            $this->receta_id = null;
            $this->resolverPacienteDesdeReferencias();
            return;
        }

        $this->receta_id = $recetaId;
        $this->paciente_id = (int) ($receta['patient_id'] ?? 0) ?: null;
        $this->pacienteBloqueado = $this->paciente_id !== null;

        if (!empty($receta['ticket_id'])) {
            $this->ticket_id = (int) $receta['ticket_id'];
        }
    }

    public function updatedPacienteId(mixed $value): void
    {
        if ($this->pacienteBloqueado) {
            return;
        }

        $patientId = (int) $value;
        $this->paciente_id = $patientId > 0 ? $patientId : null;
    }

    public function agregarItem(): void
    {
        $this->items[] = $this->itemPorDefecto();
    }

    public function quitarItem(int $index): void
    {
        if (count($this->items) === 1) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function alternarDetalleLente(int $index): void
    {
        if (!isset($this->items[$index])) {
            return;
        }

        $actual = (bool) ($this->items[$index]['requiere_lente'] ?? false);
        $this->items[$index]['requiere_lente'] = !$actual;
    }

    public function guardar()
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        if (!Schema::hasTable('erp_ordenes_trabajo')) {
            return redirect()->to(group_route('erp.work-orders.index'))
                ->with('status', 'La tabla erp_ordenes_trabajo aun no existe. Ejecuta migraciones primero.');
        }

        $validated = $this->validate($this->reglas(), $this->mensajes());
        $paciente = $this->resolverPaciente($tenantId, $groupCompanyId, (int) $validated['paciente_id']);
        $ticket = $this->resolverTicketSeleccionado($tenantId, $groupCompanyId, $validated['ticket_id'] ?? null);
        $receta = $this->resolverRecetaSeleccionada($tenantId, $groupCompanyId, $validated['receta_id'] ?? null);

        $this->validarCoherenciaPaciente($paciente['id'], $ticket, $receta);

        $numeroOt = $this->siguienteNumeroOt($tenantId, $this->codigoGrupoActual());
        $ordenTrabajo = OrdenTrabajo::query()->create([
            'tenant_id' => $tenantId,
            'group_company_id' => $groupCompanyId,
            'ticket_id' => $ticket?->id,
            'receta_id' => $receta?->id,
            'paciente_id' => (int) $paciente['id'],
            'numero_ot' => $numeroOt,
            'fecha_orden' => $validated['fecha_orden'],
            'fecha_prometida' => $validated['fecha_prometida'] ?: null,
            'tipo_orden' => $validated['tipo_orden'],
            'estado_ot' => $validated['estado_ot'],
            'prioridad' => $validated['prioridad'],
            'observaciones' => $validated['observaciones'] ?: null,
            'indicaciones_entrega' => $validated['indicaciones_entrega'] ?: null,
            'estado' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        foreach (array_values($validated['items']) as $index => $item) {
            $detalle = OrdenTrabajoDetalle::query()->create([
                'orden_trabajo_id' => $ordenTrabajo->id,
                'secuencia' => $index + 1,
                'tipo_detalle' => $item['tipo_detalle'],
                'catalogo_id' => !empty($item['catalogo_id']) ? (int) $item['catalogo_id'] : null,
                'matriz_lente_id' => !empty($item['matriz_lente_id']) ? (int) $item['matriz_lente_id'] : null,
                'descripcion' => $item['descripcion'],
                'cantidad' => (float) $item['cantidad'],
                'unidad' => $item['unidad'],
                'precio_unitario' => $this->normalizarDecimal($item['precio_unitario'] ?? null),
                'subtotal' => $this->resolverSubtotalItem($item),
                'estado_detalle' => 'pendiente',
                'observaciones' => $item['observaciones'] ?: null,
                'estado' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            if ($this->requierePersistirLente($item)) {
                $lente = $item['lente'] ?? [];

                OrdenTrabajoLente::query()->create([
                    'orden_trabajo_detalle_id' => $detalle->id,
                    'tipo_vision' => $lente['tipo_vision'] ?: null,
                    'material_id' => $this->normalizarEntero($lente['material_id'] ?? null),
                    'tratamiento_id' => $this->normalizarEntero($lente['tratamiento_id'] ?? null),
                    'color_id' => $this->normalizarEntero($lente['color_id'] ?? null),
                    'diseno_id' => $this->normalizarEntero($lente['diseno_id'] ?? null),
                    'indice_id' => $this->normalizarEntero($lente['indice_id'] ?? null),
                    'od_esferico' => $this->normalizarDecimal($lente['od_esferico'] ?? null),
                    'od_cilindro' => $this->normalizarDecimal($lente['od_cilindro'] ?? null),
                    'od_eje' => $this->normalizarEntero($lente['od_eje'] ?? null),
                    'od_adicion' => $this->normalizarDecimal($lente['od_adicion'] ?? null),
                    'oi_esferico' => $this->normalizarDecimal($lente['oi_esferico'] ?? null),
                    'oi_cilindro' => $this->normalizarDecimal($lente['oi_cilindro'] ?? null),
                    'oi_eje' => $this->normalizarEntero($lente['oi_eje'] ?? null),
                    'oi_adicion' => $this->normalizarDecimal($lente['oi_adicion'] ?? null),
                    'dp' => $this->normalizarDecimal($lente['dp'] ?? null),
                    'altura_oblea' => $this->normalizarDecimal($lente['altura_oblea'] ?? null),
                    'observaciones_tecnicas' => $lente['observaciones_tecnicas'] ?: null,
                    'estado' => true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        }

        OrdenTrabajoHistorial::query()->create([
            'orden_trabajo_id' => $ordenTrabajo->id,
            'estado_anterior' => null,
            'estado_nuevo' => $validated['estado_ot'],
            'observacion' => 'Creacion inicial de la orden de trabajo.',
            'changed_by' => auth()->id(),
            'created_at' => now(),
        ]);

        return redirect()->to(group_route('erp.work-orders.index'))
            ->with('status', "Orden de trabajo {$numeroOt} creada correctamente.");
    }

    public function render()
    {
        return view('erp::livewire.orden-trabajo-form', [
            'tipoOrdenOptions' => [
                'lentes_completos' => 'Lentes completos',
                'cambio_lunas' => 'Cambio de lunas',
                'contactologia' => 'Contactologia',
                'servicio_externo' => 'Servicio externo',
                'reparacion' => 'Reparacion',
            ],
            'estadoOtOptions' => [
                'pendiente' => 'Pendiente',
                'en_laboratorio' => 'En laboratorio',
                'control_calidad' => 'Control de calidad',
                'listo' => 'Listo',
                'entregado' => 'Entregado',
                'anulado' => 'Anulado',
            ],
            'prioridadOptions' => [
                'normal' => 'Normal',
                'urgente' => 'Urgente',
            ],
            'tipoDetalleOptions' => [
                'producto' => 'Producto',
                'lente' => 'Lente',
                'servicio' => 'Servicio',
                'accesorio' => 'Accesorio',
                'reparacion' => 'Reparacion',
            ],
            'tipoVisionOptions' => [
                'monofocal' => 'Monofocal',
                'bifocal' => 'Bifocal',
                'progresivo' => 'Progresivo',
                'ocupacional' => 'Ocupacional',
                'contacto' => 'Contacto',
            ],
        ]);
    }

    private function cargarOpcionesFormulario(): void
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        $this->numeroOtPreview = $this->siguienteNumeroOt($tenantId, $this->codigoGrupoActual());
        $this->ticketOptions = $this->obtenerTicketsRecientes($tenantId, $groupCompanyId);
        $this->recetaOptions = $this->obtenerRecetasRecientes($tenantId, $groupCompanyId);
        $this->patientOptions = app(PatientDirectoryContract::class)->search(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            term: '',
            limit: 100
        );
        $this->catalogoOptions = $this->obtenerCatalogosActivos($tenantId, $groupCompanyId);
    }

    private function reglas(): array
    {
        $rules = [
            'ticket_id' => ['nullable', 'integer'],
            'receta_id' => ['nullable', 'integer'],
            'paciente_id' => ['required', 'integer'],
            'fecha_orden' => ['required', 'date'],
            'fecha_prometida' => ['nullable', 'date', 'after_or_equal:fecha_orden'],
            'tipo_orden' => ['required', 'string', 'in:lentes_completos,cambio_lunas,contactologia,servicio_externo,reparacion'],
            'estado_ot' => ['required', 'string', 'in:pendiente,en_laboratorio,control_calidad,listo,entregado,anulado'],
            'prioridad' => ['required', 'string', 'in:normal,urgente'],
            'observaciones' => ['nullable', 'string', 'max:4000'],
            'indicaciones_entrega' => ['nullable', 'string', 'max:4000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.tipo_detalle' => ['required', 'string', 'in:producto,lente,servicio,accesorio,reparacion'],
            'items.*.catalogo_id' => ['nullable', 'integer'],
            'items.*.matriz_lente_id' => ['nullable', 'integer'],
            'items.*.descripcion' => ['required', 'string', 'max:255'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.01'],
            'items.*.unidad' => ['required', 'string', 'max:20'],
            'items.*.precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'items.*.observaciones' => ['nullable', 'string', 'max:2000'],
            'items.*.requiere_lente' => ['boolean'],
            'items.*.lente.tipo_vision' => ['nullable', 'string', 'in:monofocal,bifocal,progresivo,ocupacional,contacto'],
            'items.*.lente.material_id' => ['nullable', 'integer'],
            'items.*.lente.tratamiento_id' => ['nullable', 'integer'],
            'items.*.lente.color_id' => ['nullable', 'integer'],
            'items.*.lente.diseno_id' => ['nullable', 'integer'],
            'items.*.lente.indice_id' => ['nullable', 'integer'],
            'items.*.lente.od_esferico' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'items.*.lente.od_cilindro' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'items.*.lente.od_eje' => ['nullable', 'integer', 'between:0,180'],
            'items.*.lente.od_adicion' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'items.*.lente.oi_esferico' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'items.*.lente.oi_cilindro' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'items.*.lente.oi_eje' => ['nullable', 'integer', 'between:0,180'],
            'items.*.lente.oi_adicion' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'items.*.lente.dp' => ['nullable', 'numeric', 'between:0,999.99'],
            'items.*.lente.altura_oblea' => ['nullable', 'numeric', 'between:0,999.99'],
            'items.*.lente.observaciones_tecnicas' => ['nullable', 'string', 'max:4000'],
        ];

        return $rules;
    }

    private function mensajes(): array
    {
        return [
            'paciente_id.required' => 'Selecciona un paciente.',
            'fecha_prometida.after_or_equal' => 'La fecha prometida no puede ser menor a la fecha de orden.',
            'items.min' => 'Debes registrar al menos un item.',
            'items.*.descripcion.required' => 'Cada item debe tener una descripcion.',
            'items.*.cantidad.min' => 'La cantidad de cada item debe ser mayor a cero.',
        ];
    }

    private function resolverPaciente(int $tenantId, ?int $groupCompanyId, int $pacienteId): array
    {
        $paciente = app(PatientDirectoryContract::class)->findById(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            patientId: $pacienteId
        );

        if (!$paciente) {
            throw ValidationException::withMessages([
                'paciente_id' => 'El paciente seleccionado no es valido para este contexto.',
            ]);
        }

        return $paciente;
    }

    private function resolverTicketSeleccionado(int $tenantId, ?int $groupCompanyId, mixed $ticketId): ?Ticket
    {
        if (empty($ticketId)) {
            return null;
        }

        $ticket = Ticket::query()
            ->tenant($tenantId)
            ->whereKey((int) $ticketId)
            ->where(function (Builder $scope) use ($groupCompanyId): void {
                if ($groupCompanyId !== null) {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');

                    return;
                }

                $scope->whereNull('group_company_id');
            })
            ->first();

        if (!$ticket) {
            throw ValidationException::withMessages([
                'ticket_id' => 'El ticket seleccionado no pertenece al contexto actual.',
            ]);
        }

        return $ticket;
    }

    private function resolverRecetaSeleccionada(int $tenantId, ?int $groupCompanyId, mixed $recetaId): ?Receta
    {
        if (empty($recetaId)) {
            return null;
        }

        $receta = Receta::query()
            ->tenant($tenantId)
            ->whereKey((int) $recetaId)
            ->where(function (Builder $scope) use ($groupCompanyId): void {
                if ($groupCompanyId !== null) {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');

                    return;
                }

                $scope->whereNull('group_company_id');
            })
            ->first();

        if (!$receta) {
            throw ValidationException::withMessages([
                'receta_id' => 'La receta seleccionada no pertenece al contexto actual.',
            ]);
        }

        return $receta;
    }

    private function validarCoherenciaPaciente(int $pacienteId, ?Ticket $ticket, ?Receta $receta): void
    {
        if ($ticket && (int) $ticket->paciente_id !== $pacienteId) {
            throw ValidationException::withMessages([
                'ticket_id' => 'El ticket seleccionado no corresponde al paciente elegido.',
            ]);
        }

        if ($receta && (int) $receta->paciente_id !== $pacienteId) {
            throw ValidationException::withMessages([
                'receta_id' => 'La receta seleccionada no corresponde al paciente elegido.',
            ]);
        }

        if ($ticket && $receta && $receta->ticket_id !== null && (int) $receta->ticket_id !== (int) $ticket->id) {
            throw ValidationException::withMessages([
                'receta_id' => 'La receta seleccionada pertenece a otro ticket.',
            ]);
        }
    }

    private function resolverPacienteDesdeReferencias(): void
    {
        if ($this->receta_id !== null) {
            $receta = collect($this->recetaOptions)->firstWhere('id', $this->receta_id);
            $this->paciente_id = $receta ? (int) ($receta['patient_id'] ?? 0) ?: null : null;
            $this->pacienteBloqueado = $this->paciente_id !== null;

            return;
        }

        if ($this->ticket_id !== null) {
            $ticket = collect($this->ticketOptions)->firstWhere('id', $this->ticket_id);
            $this->paciente_id = $ticket ? (int) ($ticket['patient_id'] ?? 0) ?: null : null;
            $this->pacienteBloqueado = $this->paciente_id !== null;

            return;
        }

        $this->paciente_id = null;
        $this->pacienteBloqueado = false;
    }

    private function itemPorDefecto(): array
    {
        return [
            'tipo_detalle' => 'producto',
            'catalogo_id' => null,
            'matriz_lente_id' => null,
            'descripcion' => '',
            'cantidad' => 1,
            'unidad' => 'UND',
            'precio_unitario' => '',
            'observaciones' => '',
            'requiere_lente' => false,
            'lente' => [
                'tipo_vision' => '',
                'material_id' => '',
                'tratamiento_id' => '',
                'color_id' => '',
                'diseno_id' => '',
                'indice_id' => '',
                'od_esferico' => '',
                'od_cilindro' => '',
                'od_eje' => '',
                'od_adicion' => '',
                'oi_esferico' => '',
                'oi_cilindro' => '',
                'oi_eje' => '',
                'oi_adicion' => '',
                'dp' => '',
                'altura_oblea' => '',
                'observaciones_tecnicas' => '',
            ],
        ];
    }

    private function requierePersistirLente(array $item): bool
    {
        if (!($item['requiere_lente'] ?? false)) {
            return false;
        }

        return true;
    }

    private function resolverSubtotalItem(array $item): ?float
    {
        $cantidad = (float) ($item['cantidad'] ?? 0);
        $precioUnitario = $this->normalizarDecimal($item['precio_unitario'] ?? null);

        if ($precioUnitario === null) {
            return null;
        }

        return round($cantidad * $precioUnitario, 2);
    }

    private function normalizarDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

    private function normalizarEntero(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function obtenerTicketsRecientes(int $tenantId, ?int $groupCompanyId): array
    {
        return Ticket::query()
            ->with([
                'paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento',
                'recetas:id,ticket_id,receta_numero',
            ])
            ->tenant($tenantId)
            ->where('estado', true)
            ->where('estado_ticket', '!=', 'anulado')
            ->when($groupCompanyId !== null, function (Builder $query) use ($groupCompanyId): void {
                $query->where(function (Builder $scope) use ($groupCompanyId): void {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');
                });
            })
            ->orderByDesc('fecha_ticket')
            ->limit(50)
            ->get()
            ->map(function (Ticket $ticket): array {
                $paciente = trim((string) ($ticket->paciente?->nombre_completo ?: implode(' ', array_filter([
                    $ticket->paciente?->nombres,
                    $ticket->paciente?->apellido_paterno,
                    $ticket->paciente?->apellido_materno,
                ]))));
                $documento = trim(implode(' ', array_filter([
                    $ticket->paciente?->tipo_documento,
                    $ticket->paciente?->numero_documento,
                ])));
                $pacienteConDocumento = trim($paciente . ($documento !== '' ? " ({$documento})" : ''));

                return [
                    'id' => (int) $ticket->id,
                    'label' => trim($ticket->ticket_numero . ' - ' . ($pacienteConDocumento !== '' ? $pacienteConDocumento : 'Sin paciente')),
                    'patient_id' => (int) $ticket->paciente_id,
                    'default_receta_id' => $ticket->recetas->sortByDesc('id')->first()?->id,
                ];
            })
            ->all();
    }

    private function obtenerRecetasRecientes(int $tenantId, ?int $groupCompanyId): array
    {
        return Receta::query()
            ->with([
                'paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento',
                'ticket:id,ticket_numero',
            ])
            ->tenant($tenantId)
            ->activos()
            ->when($groupCompanyId !== null, function (Builder $query) use ($groupCompanyId): void {
                $query->where(function (Builder $scope) use ($groupCompanyId): void {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');
                });
            })
            ->orderByDesc('fecha_receta')
            ->limit(50)
            ->get()
            ->map(function (Receta $receta): array {
                $paciente = trim((string) ($receta->paciente?->nombre_completo ?: implode(' ', array_filter([
                    $receta->paciente?->nombres,
                    $receta->paciente?->apellido_paterno,
                    $receta->paciente?->apellido_materno,
                ]))));
                $documento = trim(implode(' ', array_filter([
                    $receta->paciente?->tipo_documento,
                    $receta->paciente?->numero_documento,
                ])));
                $pacienteConDocumento = trim($paciente . ($documento !== '' ? " ({$documento})" : ''));

                return [
                    'id' => (int) $receta->id,
                    'label' => trim($receta->receta_numero . ' - ' . ($pacienteConDocumento !== '' ? $pacienteConDocumento : 'Sin paciente')),
                    'patient_id' => (int) $receta->paciente_id,
                    'ticket_id' => $receta->ticket_id ? (int) $receta->ticket_id : null,
                ];
            })
            ->all();
    }

    private function obtenerCatalogosActivos(int $tenantId, ?int $groupCompanyId): array
    {
        if (!Schema::hasTable('erp_catalogos')) {
            return [];
        }

        return Catalogo::query()
            ->select(['id', 'codigo', 'descripcion', 'categoria_id'])
            ->where('estado', 1)
            ->orderBy('descripcion')
            ->limit(100)
            ->get()
            ->map(fn(Catalogo $catalogo) => [
                'id' => (int) $catalogo->id,
                'label' => trim(($catalogo->codigo ? $catalogo->codigo . ' - ' : '') . ($catalogo->descripcion ?? 'Catalogo sin descripcion')),
            ])
            ->all();
    }

    /**
     * @return array{0:int,1:int|null}
     */
    private function contextoActual(): array
    {
        $group = current_group();
        $tenantId = (int) ($group?->tenant_id ?? auth()->user()?->tenant_id ?? 0);
        $groupCompanyId = $group?->id ? (int) $group->id : null;

        abort_if($tenantId <= 0, 403, 'No hay contexto de tenant activo.');

        return [$tenantId, $groupCompanyId];
    }

    private function codigoGrupoActual(): string
    {
        return strtoupper((string) (current_group_code() ?? request()->route('group') ?? 'PE'));
    }

    private function siguienteNumeroOt(int $tenantId, string $groupCode): string
    {
        $prefix = "OT-{$groupCode}-";
        $ultimoNumero = OrdenTrabajo::query()
            ->tenant($tenantId)
            ->where('numero_ot', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('numero_ot');

        $secuencia = 1;
        if (is_string($ultimoNumero) && preg_match('/(\d+)$/', $ultimoNumero, $coincidencias) === 1) {
            $secuencia = ((int) $coincidencias[1]) + 1;
        }

        while (true) {
            $candidato = sprintf('%s%06d', $prefix, $secuencia);
            $existe = OrdenTrabajo::query()
                ->where('tenant_id', $tenantId)
                ->where('numero_ot', $candidato)
                ->exists();

            if (!$existe) {
                return $candidato;
            }

            $secuencia++;
        }
    }
}
