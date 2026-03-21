<?php

namespace Modules\ERP\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\ERP\Models\RecetaContactologia;
use Modules\ERP\Models\Receta;
use Modules\ERP\Models\RecetaGraduacion;
use Modules\ERP\Models\RecetaLentesEnUso;
use Modules\ERP\Models\RecetaOftalmica;
use Modules\ERP\Models\Ticket;

class RecetaController extends BaseController
{
    public function __construct(
        private readonly PatientDirectoryContract $directorioPacientes
    ) {
    }

    public function index(Request $request): View
    {
        return view('erp::recetas.index');
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (!Schema::hasTable('erp_recetas')) {
            return redirect()->to(group_route('erp.recetas.index'))
                ->with('status', 'La tabla erp_recetas aun no existe. Ejecuta migraciones primero.');
        }

        return view('erp::recetas.create');
    }

    public function edit(Request $request, string $locale, string $group, string $receta): View|RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        if (!Schema::hasTable('erp_recetas')) {
            return redirect()->to(group_route('erp.recetas.index'))
                ->with('status', 'La tabla erp_recetas aun no existe. Ejecuta migraciones primero.');
        }

        $recetaId = (int) $receta;
        abort_if($recetaId <= 0, 404);

        $recetaModel = $this->obtenerRecetaEnContexto($recetaId, $tenantId, $groupCompanyId);

        return view('erp::recetas.edit', [
            'recetaId' => (int) $recetaModel->id,
        ]);
    }

    public function detalle(Request $request, string $locale, string $group, string $receta): View|RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        if (!Schema::hasTable('erp_recetas')) {
            return redirect()->to(group_route('erp.recetas.index'))
                ->with('status', 'La tabla erp_recetas aun no existe. Ejecuta migraciones primero.');
        }

        $recetaId = (int) $receta;
        abort_if($recetaId <= 0, 404);

        $recetaModel = $this->obtenerRecetaEnContexto($recetaId, $tenantId, $groupCompanyId);

        $this->asegurarDetallesBase($recetaModel);

        return view('erp::recetas.detalle', [
            'recetaId' => (int) $recetaModel->id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        if (!Schema::hasTable('erp_recetas')) {
            return redirect()->to(group_route('erp.recetas.index'))
                ->with('status', 'La tabla erp_recetas aun no existe. Ejecuta migraciones primero.');
        }

        $validated = $request->validate(
            [
                'ticket_id' => ['nullable', 'integer'],
                'paciente_id' => ['required', 'integer'],
                'paciente_label' => ['required', 'string', 'max:255'],
                'especialista_id' => ['nullable', 'integer', 'exists:users,id'],
                'fecha_receta' => ['required', 'date'],
                'estado_receta' => ['required', 'string', 'in:borrador,emitida,cerrada,anulada'],
                'motivo_consulta' => ['nullable', 'string', 'max:4000'],
                'observaciones_generales' => ['nullable', 'string', 'max:4000'],
            ],
            [
                'paciente_id.required' => 'Selecciona un paciente valido de la lista.',
                'paciente_id.integer' => 'Selecciona un paciente valido de la lista.',
                'paciente_label.required' => 'Selecciona un paciente.',
            ]
        );

        $paciente = $this->directorioPacientes->findById(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            patientId: (int) $validated['paciente_id']
        );

        if (!$paciente) {
            throw ValidationException::withMessages([
                'paciente_id' => 'El paciente seleccionado no es valido para este contexto.',
            ]);
        }

        $ticket = null;
        if (!empty($validated['ticket_id'])) {
            $ticket = Ticket::query()
                ->tenant($tenantId)
                ->whereKey((int) $validated['ticket_id'])
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

            if ((int) $ticket->paciente_id !== (int) $validated['paciente_id']) {
                throw ValidationException::withMessages([
                    'ticket_id' => 'El ticket seleccionado no corresponde al paciente elegido.',
                ]);
            }
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
            'motivo_consulta' => $validated['motivo_consulta'] ?? null,
            'observaciones_generales' => $validated['observaciones_generales'] ?? null,
            'estado' => 1,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->asegurarDetallesBase($receta);

        return redirect()->to(group_route('erp.recetas.index'))
            ->with('status', 'Receta creada correctamente.');
    }

    public function buscarPacientes(Request $request): JsonResponse
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $pacientes = $this->directorioPacientes->search(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            term: trim((string) ($validated['q'] ?? '')),
            limit: (int) ($validated['limit'] ?? 20)
        );

        return response()->json([
            'success' => true,
            'data' => $pacientes,
            'message' => 'Pacientes obtenidos correctamente.',
            'errors' => null,
        ]);
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

    private function obtenerTicketsRecientes(int $tenantId, ?int $groupCompanyId): array
    {
        return Ticket::query()
            ->with(['paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento'])
            ->tenant($tenantId)
            ->where('estado', true)
            ->where('estado_ticket', '!=', 'anulado')
            ->whereDoesntHave('recetas')
            ->when($groupCompanyId !== null, function (Builder $query) use ($groupCompanyId): void {
                $query->where(function (Builder $scope) use ($groupCompanyId): void {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');
                });
            })
            ->orderByDesc('fecha_ticket')
            ->limit(20)
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

    private function obtenerRecetaEnContexto(int $recetaId, int $tenantId, ?int $groupCompanyId): Receta
    {
        return Receta::query()
            ->tenant($tenantId)
            ->when($groupCompanyId !== null, function (Builder $query) use ($groupCompanyId): void {
                $query->where(function (Builder $scope) use ($groupCompanyId): void {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');
                });
            })
            ->findOrFail($recetaId);
    }

    private function asegurarDetallesBase(Receta $receta): void
    {
        if (Schema::hasTable('erp_receta_oftalmicas') && !$receta->oftalmica()->exists()) {
            RecetaOftalmica::query()->create([
                'receta_id' => $receta->id,
                'estado' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        if (Schema::hasTable('erp_receta_graduaciones') && !$receta->graduacion()->exists()) {
            RecetaGraduacion::query()->create([
                'receta_id' => $receta->id,
                'estado' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        if (Schema::hasTable('erp_receta_contactologia') && !$receta->contactologia()->exists()) {
            RecetaContactologia::query()->create([
                'receta_id' => $receta->id,
                'estado' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        if (Schema::hasTable('erp_receta_lentes_en_uso') && !$receta->lentesEnUso()->exists()) {
            RecetaLentesEnUso::query()->create([
                'receta_id' => $receta->id,
                'estado' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
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
