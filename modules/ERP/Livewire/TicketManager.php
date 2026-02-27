<?php

namespace Modules\ERP\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\ERP\Models\Ticket;

class TicketManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'ticket')]
    public string $ticketFilter = '';

    #[Url(as: 'paciente')]
    public string $patientFilter = '';

    #[Url(as: 'estado')]
    public string $estadoFilter = '';

    #[Url(as: 'fecha')]
    public string $fechaFilter = '';

    #[Url(as: 'pp')]
    public int $perPage = 15;

    #[Url(as: 'sort')]
    public string $sortField = 'fecha_ticket';

    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    private const PER_PAGE_OPTIONS = [10, 15, 25, 50];

    /**
     * @var array<int,string>
     */
    private const ESTADOS_VALIDOS = ['abierto', 'en_proceso', 'listo', 'cerrado', 'anulado'];

    /**
     * @var array<int,string>
     */
    private const SORTABLE_FIELDS = [
        'ticket_numero',
        'fecha_ticket',
        'estado_ticket',
        'total',
        'saldo_pendiente',
        'prioridad',
    ];

    #[Computed]
    public function hasTicketsTable(): bool
    {
        return Schema::hasTable('erp_tickets');
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return trim($this->ticketFilter) !== ''
            || trim($this->patientFilter) !== ''
            || trim($this->estadoFilter) !== ''
            || trim($this->fechaFilter) !== '';
    }

    /**
     * @return array{hoy:int,abiertos:int,en_proceso:int,cerrados:int,total:int}
     */
    #[Computed]
    public function stats(): array
    {
        if (!$this->hasTicketsTable) {
            return [
                'hoy' => 0,
                'abiertos' => 0,
                'en_proceso' => 0,
                'cerrados' => 0,
                'total' => 0,
            ];
        }

        $baseQuery = $this->buildBaseQuery();

        return [
            'hoy' => (clone $baseQuery)->whereDate('fecha_ticket', now()->toDateString())->count(),
            'abiertos' => (clone $baseQuery)->where('estado_ticket', 'abierto')->count(),
            'en_proceso' => (clone $baseQuery)->where('estado_ticket', 'en_proceso')->count(),
            'cerrados' => (clone $baseQuery)->where('estado_ticket', 'cerrado')->count(),
            'total' => (clone $baseQuery)->count(),
        ];
    }

    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        if (!$this->hasTicketsTable) {
            return new LengthAwarePaginator(
                collect(),
                0,
                $this->resolvedPerPage(),
                1,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        }

        $query = $this->buildBaseQuery();
        $this->applyFilters($query);

        return $query
            ->orderBy($this->resolvedSortField(), $this->resolvedSortDirection())
            ->paginate($this->resolvedPerPage());
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    #[Computed]
    public function patientOptions(): array
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        $term = trim($this->patientFilter);
        if ($term !== '' && mb_strlen($term) < 2) {
            return [];
        }

        return app(PatientDirectoryContract::class)->search(
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
            term: $term,
            limit: 15
        );
    }

    public function updatedTicketFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPatientFilter(): void
    {
        $this->resetPage();
    }

    public function updatedEstadoFilter(): void
    {
        $this->resetPage();
    }

    public function updatedFechaFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(mixed $value): void
    {
        $perPage = (int) $value;
        $this->perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 15;
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (!in_array($field, self::SORTABLE_FIELDS, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['ticketFilter', 'patientFilter', 'estadoFilter', 'fechaFilter']);
        $this->resetPage();
    }

    public function render()
    {
        return view('erp::livewire.ticket-manager', [
            'estadoMeta' => [
                'abierto' => ['label' => 'Abierto', 'class' => 'bg-label-warning'],
                'en_proceso' => ['label' => 'En proceso', 'class' => 'bg-label-success'],
                'listo' => ['label' => 'Listo', 'class' => 'bg-label-primary'],
                'cerrado' => ['label' => 'Cerrado', 'class' => 'bg-label-info'],
                'anulado' => ['label' => 'Anulado', 'class' => 'bg-label-danger'],
            ],
        ]);
    }

    private function buildBaseQuery(): Builder
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        $query = Ticket::query()
            ->with([
                'paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento',
                'creadoPor:id,name',
            ])
            ->tenant($tenantId)
            ->activos();

        if ($groupCompanyId !== null) {
            $query->where(function (Builder $scope) use ($groupCompanyId): void {
                $scope->where('group_company_id', $groupCompanyId)
                    ->orWhereNull('group_company_id');
            });
        }

        return $query;
    }

    private function applyFilters(Builder $query): void
    {
        $ticketFilter = trim($this->ticketFilter);
        if ($ticketFilter !== '') {
            $like = '%' . $this->escapeLike($ticketFilter) . '%';
            $query->where('ticket_numero', 'like', $like);
        }

        $patientFilter = trim($this->patientFilter);
        if ($patientFilter !== '') {
            $patientTerms = $this->resolvePatientSearchTerms($patientFilter);
            $query->whereHas('paciente', function (Builder $patientQuery) use ($patientTerms): void {
                $patientQuery->where(function (Builder $scope) use ($patientTerms): void {
                    foreach ($patientTerms as $index => $term) {
                        $like = '%' . $this->escapeLike($term) . '%';

                        if ($index === 0) {
                            $scope->where('nombre_completo', 'like', $like)
                                ->orWhere('nombres', 'like', $like)
                                ->orWhere('numero_documento', 'like', $like);
                            continue;
                        }

                        $scope->orWhere('nombre_completo', 'like', $like)
                            ->orWhere('nombres', 'like', $like)
                            ->orWhere('numero_documento', 'like', $like);
                    }
                });
            });
        }

        $estadoFilter = trim($this->estadoFilter);
        if ($estadoFilter !== '' && in_array($estadoFilter, self::ESTADOS_VALIDOS, true)) {
            $query->where('estado_ticket', $estadoFilter);
        }

        $fechaFilter = trim($this->fechaFilter);
        if ($fechaFilter !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFilter) === 1) {
            $query->whereDate('fecha_ticket', $fechaFilter);
        }
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }

    /**
     * @return array<int,string>
     */
    private function resolvePatientSearchTerms(string $value): array
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        if ($value === '') {
            return [];
        }

        $terms = [$value];

        if (str_contains($value, '(')) {
            $namePart = trim((string) strtok($value, '('));
            if ($namePart !== '') {
                $terms[] = $namePart;
            }
        }

        if (preg_match('/\(([^)]*)\)/', $value, $matches) === 1) {
            $inside = trim((string) ($matches[1] ?? ''));
            if ($inside !== '') {
                $terms[] = $inside;

                $tokens = preg_split('/\s+/', $inside) ?: [];
                foreach ($tokens as $token) {
                    $token = trim($token);
                    if ($token !== '' && mb_strlen($token) >= 3) {
                        $terms[] = $token;
                    }
                }
            }
        }

        return array_values(array_unique($terms));
    }

    private function resolvedSortField(): string
    {
        return in_array($this->sortField, self::SORTABLE_FIELDS, true)
            ? $this->sortField
            : 'fecha_ticket';
    }

    private function resolvedSortDirection(): string
    {
        return $this->sortDirection === 'asc' ? 'asc' : 'desc';
    }

    private function resolvedPerPage(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true)
            ? $this->perPage
            : 15;
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
}
