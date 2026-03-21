<?php

namespace Modules\ERP\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\ERP\Models\RecetaContactologia;
use Modules\ERP\Models\Receta;
use Modules\ERP\Models\RecetaGraduacion;
use Modules\ERP\Models\RecetaLentesEnUso;
use Modules\ERP\Models\RecetaOftalmica;
use Modules\ERP\Models\Ticket;

class RecetaForm extends Component
{
    public ?int $recetaId = null;
    public ?int $ticket_id = null;
    public ?int $paciente_id = null;
    public string $paciente_label = '';
    public ?int $especialista_id = null;
    public string $fecha_receta = '';
    public string $estado_receta = 'borrador';
    public string $motivo_consulta = '';
    public string $observaciones_generales = '';
    public bool $pacienteBloqueado = false;
    public array $patientOptions = [];
    public array $ticketOptions = [];
    public array $specialistOptions = [];
    public string $recipeNumberPreview = 'RC-000000';

    public function mount(?int $recetaId = null): void
    {
        $this->recetaId = $recetaId;

        $this->fecha_receta = now()->format('Y-m-d\TH:i');
        $this->estado_receta = 'borrador';

        $this->cargarOpcionesFormulario();

        if ($this->recetaId !== null) {
            $this->cargarReceta();
        }
    }

    public function updatedTicketId(mixed $value): void
    {
        $ticketId = (int) $value;

        if ($ticketId <= 0) {
            $this->ticket_id = null;
            $this->pacienteBloqueado = false;
            $this->paciente_id = null;
            $this->paciente_label = '';
            return;
        }

        $ticket = collect($this->ticketOptions)->firstWhere('id', $ticketId);

        if (!$ticket) {
            $this->ticket_id = null;
            $this->pacienteBloqueado = false;
            $this->paciente_id = null;
            $this->paciente_label = '';
            return;
        }

        $this->ticket_id = $ticketId;
        $this->paciente_id = (int) ($ticket['patient_id'] ?? 0) ?: null;
        $this->paciente_label = (string) ($ticket['patient_label'] ?? '');
        $this->pacienteBloqueado = $this->paciente_id !== null && $this->paciente_label !== '';
    }

    public function guardar()
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        if (!Schema::hasTable('erp_recetas')) {
            return redirect()->to(group_route('erp.recetas.index'))
                ->with('status', 'La tabla erp_recetas aun no existe. Ejecuta migraciones primero.');
        }

        $validated = $this->validate($this->reglas(), $this->mensajes());

        $paciente = app(PatientDirectoryContract::class)->findById(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            patientId: (int) $validated['paciente_id']
        );

        if (!$paciente) {
            throw ValidationException::withMessages([
                'paciente_id' => 'El paciente seleccionado no es valido para este contexto.',
            ]);
        }

        $ticket = $this->resolverTicketSeleccionado($tenantId, $groupCompanyId, $validated['ticket_id'] ?? null, (int) $validated['paciente_id']);

        if ($this->recetaId !== null) {
            $receta = $this->obtenerRecetaEnContexto($this->recetaId, $tenantId, $groupCompanyId);
            $receta->update([
                'ticket_id' => $ticket?->id,
                'paciente_id' => (int) $validated['paciente_id'],
                'especialista_id' => !empty($validated['especialista_id']) ? (int) $validated['especialista_id'] : null,
                'fecha_receta' => $validated['fecha_receta'],
                'estado_receta' => $validated['estado_receta'],
                'motivo_consulta' => $validated['motivo_consulta'] ?: null,
                'observaciones_generales' => $validated['observaciones_generales'] ?: null,
                'updated_by' => auth()->id(),
            ]);

            session()->flash('status', 'Receta actualizada correctamente.');

            return redirect()->to(group_route('erp.recetas.index'));
        }

        $receta = Receta::query()->create([
            'tenant_id' => $tenantId,
            'group_company_id' => $groupCompanyId,
            'ticket_id' => $ticket?->id,
            'paciente_id' => (int) $validated['paciente_id'],
            'especialista_id' => !empty($validated['especialista_id']) ? (int) $validated['especialista_id'] : null,
            'receta_numero' => $this->siguienteNumeroReceta($tenantId, $this->codigoGrupoActual()),
            'fecha_receta' => $validated['fecha_receta'],
            'tipo_receta' => 'integral',
            'estado_receta' => $validated['estado_receta'],
            'motivo_consulta' => $validated['motivo_consulta'] ?: null,
            'observaciones_generales' => $validated['observaciones_generales'] ?: null,
            'estado' => 1,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->crearDetallesBaseSiFaltan($receta);

        session()->flash('status', 'Receta creada correctamente.');

        return redirect()->to(group_route('erp.recetas.index'));
    }

    public function render()
    {
        return view('erp::livewire.receta-form', [
            'esEdicion' => $this->recetaId !== null,
        ]);
    }

    private function reglas(): array
    {
        return [
            'ticket_id' => ['nullable', 'integer'],
            'paciente_id' => ['required', 'integer'],
            'paciente_label' => ['required', 'string', 'max:255'],
            'especialista_id' => ['nullable', 'integer', 'exists:users,id'],
            'fecha_receta' => ['required', 'date'],
            'estado_receta' => ['required', 'string', 'in:borrador,emitida,cerrada,anulada'],
            'motivo_consulta' => ['nullable', 'string', 'max:4000'],
            'observaciones_generales' => ['nullable', 'string', 'max:4000'],
        ];
    }

    private function mensajes(): array
    {
        return [
            'paciente_id.required' => 'Selecciona un paciente valido de la lista.',
            'paciente_id.integer' => 'Selecciona un paciente valido de la lista.',
            'paciente_label.required' => 'Selecciona un paciente.',
        ];
    }

    private function cargarOpcionesFormulario(): void
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        $this->patientOptions = app(PatientDirectoryContract::class)->search(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            term: '',
            limit: 20
        );
        $this->ticketOptions = $this->obtenerTicketsDisponibles($tenantId, $groupCompanyId, $this->recetaId);
        $this->specialistOptions = $this->obtenerEspecialistas($tenantId);
        $this->recipeNumberPreview = $this->siguienteNumeroReceta($tenantId, $this->codigoGrupoActual());
    }

    private function cargarReceta(): void
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();
        $receta = $this->obtenerRecetaEnContexto($this->recetaId, $tenantId, $groupCompanyId);

        $this->ticket_id = $receta->ticket_id ? (int) $receta->ticket_id : null;
        $this->paciente_id = (int) $receta->paciente_id;
        $this->paciente_label = trim((string) ($receta->paciente?->nombre_completo ?: implode(' ', array_filter([
            $receta->paciente?->nombres,
            $receta->paciente?->apellido_paterno,
            $receta->paciente?->apellido_materno,
        ]))));
        $documento = trim(implode(' ', array_filter([
            $receta->paciente?->tipo_documento,
            $receta->paciente?->numero_documento,
        ])));
        if ($documento !== '') {
            $this->paciente_label = trim($this->paciente_label . " ({$documento})");
        }
        $this->especialista_id = $receta->especialista_id ? (int) $receta->especialista_id : null;
        $this->fecha_receta = optional($receta->fecha_receta)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        $this->estado_receta = (string) $receta->estado_receta;
        $this->motivo_consulta = (string) ($receta->motivo_consulta ?? '');
        $this->observaciones_generales = (string) ($receta->observaciones_generales ?? '');
        $this->pacienteBloqueado = $this->ticket_id !== null;
        $this->recipeNumberPreview = (string) $receta->receta_numero;
        $this->ticketOptions = $this->obtenerTicketsDisponibles($tenantId, $groupCompanyId, $this->recetaId);
    }

    private function crearDetallesBaseSiFaltan(Receta $receta): void
    {
        if (Schema::hasTable('erp_receta_oftalmicas') && !$receta->oftalmica()->exists()) {
            RecetaOftalmica::query()->create([
                'receta_id' => $receta->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        if (Schema::hasTable('erp_receta_graduaciones') && !$receta->graduacion()->exists()) {
            RecetaGraduacion::query()->create([
                'receta_id' => $receta->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'estado' => 1,
            ]);
        }

        if (Schema::hasTable('erp_receta_contactologia') && !$receta->contactologia()->exists()) {
            RecetaContactologia::query()->create([
                'receta_id' => $receta->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'estado' => 1,
            ]);
        }

        if (Schema::hasTable('erp_receta_lentes_en_uso') && !$receta->lentesEnUso()->exists()) {
            RecetaLentesEnUso::query()->create([
                'receta_id' => $receta->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'estado' => 1,
            ]);
        }
    }

    private function resolverTicketSeleccionado(int $tenantId, ?int $groupCompanyId, mixed $ticketId, int $pacienteId): ?Ticket
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

        $tieneOtraReceta = $ticket->recetas()
            ->when($this->recetaId !== null, fn($query) => $query->where('id', '!=', $this->recetaId))
            ->exists();

        if ($tieneOtraReceta) {
            throw ValidationException::withMessages([
                'ticket_id' => 'El ticket seleccionado ya esta relacionado a otra receta.',
            ]);
        }

        if ((int) $ticket->paciente_id !== $pacienteId) {
            throw ValidationException::withMessages([
                'ticket_id' => 'El ticket seleccionado no corresponde al paciente elegido.',
            ]);
        }

        return $ticket;
    }

    private function obtenerTicketsDisponibles(int $tenantId, ?int $groupCompanyId, ?int $recetaId = null): array
    {
        $ticketActualId = null;

        if ($recetaId !== null) {
            $ticketActualId = Receta::query()
                ->whereKey($recetaId)
                ->value('ticket_id');
        }

        return Ticket::query()
            ->with(['paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento'])
            ->tenant($tenantId)
            ->where('estado', true)
            ->where('estado_ticket', '!=', 'anulado')
            ->where(function (Builder $query) use ($ticketActualId): void {
                $query->whereDoesntHave('recetas');

                if ($ticketActualId !== null) {
                    $query->orWhere('id', $ticketActualId);
                }
            })
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
                    'patient_label' => $pacienteConDocumento !== '' ? $pacienteConDocumento : 'Sin paciente',
                ];
            })
            ->all();
    }

    private function obtenerEspecialistas(int $tenantId): array
    {
        return User::query()
            ->where('tenant_id', $tenantId)
            ->where('estado', User::ESTADO_ACTIVO)
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name'])
            ->map(fn(User $user) => [
                'id' => (int) $user->id,
                'label' => (string) $user->name,
            ])
            ->all();
    }

    private function obtenerRecetaEnContexto(int $recetaId, int $tenantId, ?int $groupCompanyId): Receta
    {
        return Receta::query()
            ->with(['paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento'])
            ->tenant($tenantId)
            ->when($groupCompanyId !== null, function (Builder $query) use ($groupCompanyId): void {
                $query->where(function (Builder $scope) use ($groupCompanyId): void {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');
                });
            })
            ->findOrFail($recetaId);
    }

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

    private function siguienteNumeroReceta(int $tenantId, string $groupCode): string
    {
        $prefix = "RC-{$groupCode}-";
        $ultimoNumero = Receta::query()
            ->tenant($tenantId)
            ->where('receta_numero', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('receta_numero');

        $secuencia = 1;
        if (is_string($ultimoNumero) && preg_match('/(\d+)$/', $ultimoNumero, $coincidencias) === 1) {
            $secuencia = ((int) $coincidencias[1]) + 1;
        }

        while (true) {
            $candidato = sprintf('%s%06d', $prefix, $secuencia);
            $existe = Receta::query()
                ->where('tenant_id', $tenantId)
                ->where('receta_numero', $candidato)
                ->exists();

            if (!$existe) {
                return $candidato;
            }

            $secuencia++;
        }
    }
}
