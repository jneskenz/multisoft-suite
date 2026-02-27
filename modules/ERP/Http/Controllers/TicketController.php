<?php

namespace Modules\ERP\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\ERP\Models\Ticket;

class TicketController extends BaseController
{
    public function __construct(
        private readonly PatientDirectoryContract $patientDirectory
    ) { }

    public function index(Request $request): View
    {
        return view('erp::tickets.index');
    }

    public function create(Request $request): View|RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        if (!Schema::hasTable('erp_tickets')) {
            return redirect()->to(group_route('erp.tickets.index'))
                ->with('status', 'La tabla erp_tickets aun no existe. Ejecuta migraciones primero.');
        }

        $patientOptions = $this->patientDirectory->search(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            term: '',
            limit: 20
        );

        return view('erp::tickets.create', [
            'patientOptions' => $patientOptions,
            'ticketNumberPreview' => $this->nextTicketNumber($tenantId, $this->currentGroupCode()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        if (!Schema::hasTable('erp_tickets')) {
            return redirect()->to(group_route('erp.tickets.index'))
                ->with('status', 'La tabla erp_tickets aun no existe. Ejecuta migraciones primero.');
        }

        $validated = $request->validate(
            [
                'paciente_id' => ['required', 'integer'],
                'paciente_label' => ['required', 'string', 'max:255'],
                'fecha_ticket' => ['required', 'date'],
                'estado_ticket' => ['required', 'string', 'in:abierto,en_proceso,listo,cerrado,anulado'],
                'prioridad' => ['required', 'string', 'in:baja,normal,alta,urgente'],
                'canal' => ['nullable', 'string', 'in:mostrador,cita,telefono,web'],
                'resumen' => ['nullable', 'string', 'max:2000'],
                'moneda' => ['required', 'string', 'size:3'],
                'subtotal' => ['required', 'numeric', 'min:0'],
                'descuento_total' => ['nullable', 'numeric', 'min:0'],
                'impuesto_total' => ['nullable', 'numeric', 'min:0'],
                'saldo_pendiente' => ['nullable', 'numeric', 'min:0'],
            ],
            [
                'paciente_id.required' => 'Selecciona un paciente valido de la lista.',
                'paciente_id.integer' => 'Selecciona un paciente valido de la lista.',
                'paciente_label.required' => 'Selecciona un paciente.',
            ],
            [
                'paciente_id' => 'paciente',
                'paciente_label' => 'paciente',
                'fecha_ticket' => 'fecha de ticket',
                'estado_ticket' => 'estado',
                'descuento_total' => 'descuento',
                'impuesto_total' => 'impuesto',
                'saldo_pendiente' => 'saldo pendiente',
            ]
        );

        $patient = $this->patientDirectory->findById(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            patientId: (int) $validated['paciente_id']
        );

        if (!$patient) {
            throw ValidationException::withMessages([
                'paciente_id' => 'El paciente seleccionado no es valido para este contexto.',
            ]);
        }

        $subtotal = round((float) $validated['subtotal'], 2);
        $descuento = round((float) ($validated['descuento_total'] ?? 0), 2);
        $impuesto = round((float) ($validated['impuesto_total'] ?? 0), 2);

        if ($descuento > $subtotal) {
            throw ValidationException::withMessages([
                'descuento_total' => 'El descuento no puede ser mayor al subtotal.',
            ]);
        }

        $total = round(($subtotal - $descuento) + $impuesto, 2);
        $isClosed = in_array($validated['estado_ticket'], ['cerrado', 'anulado'], true);
        $saldoPendiente = array_key_exists('saldo_pendiente', $validated) && $validated['saldo_pendiente'] !== null
            ? round((float) $validated['saldo_pendiente'], 2)
            : ($isClosed ? 0.0 : $total);

        if ($saldoPendiente > $total) {
            throw ValidationException::withMessages([
                'saldo_pendiente' => 'El saldo pendiente no puede ser mayor al total.',
            ]);
        }

        if ($isClosed) {
            $saldoPendiente = 0.0;
        }

        Ticket::create([
            'tenant_id' => $tenantId,
            'group_company_id' => $groupCompanyId,
            'paciente_id' => (int) $validated['paciente_id'],
            'ticket_numero' => $this->nextTicketNumber($tenantId, $this->currentGroupCode()),
            'fecha_ticket' => $validated['fecha_ticket'],
            'estado_ticket' => $validated['estado_ticket'],
            'prioridad' => $validated['prioridad'],
            'canal' => $validated['canal'] ?? null,
            'resumen' => $validated['resumen'] ?? null,
            'moneda' => strtoupper((string) $validated['moneda']),
            'subtotal' => $subtotal,
            'descuento_total' => $descuento,
            'impuesto_total' => $impuesto,
            'total' => $total,
            'saldo_pendiente' => $saldoPendiente,
            'fecha_cierre' => $isClosed ? now() : null,
            'cerrado_por' => $isClosed ? auth()->id() : null,
            'estado' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->to(group_route('erp.tickets.index'))
            ->with('status', 'Ticket creado correctamente.');
    }

    public function searchPatients(Request $request): JsonResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $patients = $this->patientDirectory->search(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            term: trim((string) ($validated['q'] ?? '')),
            limit: (int) ($validated['limit'] ?? 20)
        );

        return response()->json([
            'success' => true,
            'data' => $patients,
            'message' => 'Pacientes obtenidos correctamente.',
            'errors' => null,
        ]);
    }

    /**
     * @return array{0:int,1:int|null}
     */
    private function currentContext(): array
    {
        $group = current_group();
        $tenantId = (int) ($group?->tenant_id ?? auth()->user()?->tenant_id ?? 0);
        $groupCompanyId = $group?->id ? (int) $group->id : null;

        abort_if($tenantId <= 0, 403, 'No hay contexto de tenant activo.');

        return [$tenantId, $groupCompanyId];
    }

    private function currentGroupCode(): string
    {
        return strtoupper((string) (current_group_code() ?? request()->route('group') ?? 'PE'));
    }

    private function nextTicketNumber(int $tenantId, string $groupCode): string
    {
        $prefix = "TK-{$groupCode}-";
        $lastNumber = Ticket::query()
            ->tenant($tenantId)
            ->where('ticket_numero', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('ticket_numero');

        $sequence = 1;
        if (is_string($lastNumber) && preg_match('/(\d+)$/', $lastNumber, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        while (true) {
            $candidate = sprintf('%s%06d', $prefix, $sequence);
            $exists = Ticket::query()
                ->where('tenant_id', $tenantId)
                ->where('ticket_numero', $candidate)
                ->exists();

            if (!$exists) {
                return $candidate;
            }

            $sequence++;
        }
    }
}
