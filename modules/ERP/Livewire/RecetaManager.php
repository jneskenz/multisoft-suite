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
use Modules\ERP\Models\Receta;

class RecetaManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'receta')]
    public string $recetaFilter = '';

    #[Url(as: 'paciente')]
    public string $patientFilter = '';

    #[Url(as: 'estado')]
    public string $estadoFilter = '';

    #[Url(as: 'fecha')]
    public string $fechaFilter = '';

    #[Url(as: 'pp')]
    public int $perPage = 15;

    #[Url(as: 'sort')]
    public string $sortField = 'fecha_receta';

    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    private const PER_PAGE_OPTIONS = [10, 15, 25, 50];
    private const ESTADOS_VALIDOS = ['borrador', 'emitida', 'cerrada', 'anulada'];
    private const CAMPOS_ORDENABLES = ['receta_numero', 'fecha_receta', 'estado_receta'];

    #[Computed]
    public function hasRecetasTable(): bool
    {
        return Schema::hasTable('erp_recetas');
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return trim($this->recetaFilter) !== ''
            || trim($this->patientFilter) !== ''
            || trim($this->estadoFilter) !== ''
            || trim($this->fechaFilter) !== '';
    }

    #[Computed]
    public function stats(): array
    {
        if (!$this->hasRecetasTable) {
            return ['hoy' => 0, 'borrador' => 0, 'emitida' => 0, 'anulada' => 0, 'total' => 0];
        }

        $baseQuery = $this->construirConsultaBase();

        return [
            'hoy' => (clone $baseQuery)->whereDate('fecha_receta', now()->toDateString())->count(),
            'borrador' => (clone $baseQuery)->where('estado_receta', 'borrador')->count(),
            'emitida' => (clone $baseQuery)->where('estado_receta', 'emitida')->count(),
            'anulada' => (clone $baseQuery)->where('estado_receta', 'anulada')->count(),
            'total' => (clone $baseQuery)->count(),
        ];
    }

    #[Computed]
    public function recetas(): LengthAwarePaginator
    {
        if (!$this->hasRecetasTable) {
            return new LengthAwarePaginator(collect(), 0, $this->porPaginaResuelta(), 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        $query = $this->construirConsultaBase();
        $this->aplicarFiltros($query);

        return $query
            ->orderBy($this->campoOrdenamientoResuelto(), $this->direccionOrdenamientoResuelta())
            ->paginate($this->porPaginaResuelta());
    }

    #[Computed]
    public function patientOptions(): array
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

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

    public function updatedRecetaFilter(): void { $this->resetPage(); }
    public function updatedPatientFilter(): void { $this->resetPage(); }
    public function updatedEstadoFilter(): void { $this->resetPage(); }
    public function updatedFechaFilter(): void { $this->resetPage(); }
    public function updatedPerPage(mixed $value): void
    {
        $perPage = (int) $value;
        $this->perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 15;
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (!in_array($field, self::CAMPOS_ORDENABLES, true)) {
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
        $this->reset(['recetaFilter', 'patientFilter', 'estadoFilter', 'fechaFilter']);
        $this->resetPage();
    }

    public function render()
    {
        return view('erp::livewire.receta-manager', [
            'estadoMeta' => [
                'borrador' => ['label' => 'Borrador', 'class' => 'bg-label-warning'],
                'emitida' => ['label' => 'Emitida', 'class' => 'bg-label-success'],
                'cerrada' => ['label' => 'Cerrada', 'class' => 'bg-label-info'],
                'anulada' => ['label' => 'Anulada', 'class' => 'bg-label-danger'],
            ],
        ]);
    }

    private function construirConsultaBase(): Builder
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        $query = Receta::query()
            ->with([
                'ticket:id,ticket_numero',
                'paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento',
                'especialista:id,name',
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

    private function aplicarFiltros(Builder $query): void
    {
        $recetaFilter = trim($this->recetaFilter);
        if ($recetaFilter !== '') {
            $query->where('receta_numero', 'like', '%' . $this->escaparLike($recetaFilter) . '%');
        }

        $patientFilter = trim($this->patientFilter);
        if ($patientFilter !== '') {
            $terminos = $this->resolverTerminosPaciente($patientFilter);
            $query->whereHas('paciente', function (Builder $patientQuery) use ($terminos): void {
                $patientQuery->where(function (Builder $scope) use ($terminos): void {
                    foreach ($terminos as $index => $term) {
                        $like = '%' . $this->escaparLike($term) . '%';

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
            $query->where('estado_receta', $estadoFilter);
        }

        $fechaFilter = trim($this->fechaFilter);
        if ($fechaFilter !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFilter) === 1) {
            $query->whereDate('fecha_receta', $fechaFilter);
        }
    }

    private function escaparLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    private function resolverTerminosPaciente(string $value): array
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        if ($value === '') {
            return [];
        }

        $terminos = [$value];

        if (str_contains($value, '(')) {
            $nombre = trim((string) strtok($value, '('));
            if ($nombre !== '') {
                $terminos[] = $nombre;
            }
        }

        if (preg_match('/\(([^)]*)\)/', $value, $matches) === 1) {
            $inside = trim((string) ($matches[1] ?? ''));
            if ($inside !== '') {
                $terminos[] = $inside;

                foreach (preg_split('/\s+/', $inside) ?: [] as $token) {
                    $token = trim($token);
                    if ($token !== '' && mb_strlen($token) >= 3) {
                        $terminos[] = $token;
                    }
                }
            }
        }

        return array_values(array_unique($terminos));
    }

    private function contextoActual(): array
    {
        $group = current_group();
        $tenantId = (int) ($group?->tenant_id ?? auth()->user()?->tenant_id ?? 0);
        $groupCompanyId = $group?->id ? (int) $group->id : null;

        abort_if($tenantId <= 0, 403, 'No hay contexto de tenant activo.');

        return [$tenantId, $groupCompanyId];
    }

    private function campoOrdenamientoResuelto(): string
    {
        return in_array($this->sortField, self::CAMPOS_ORDENABLES, true) ? $this->sortField : 'fecha_receta';
    }

    private function direccionOrdenamientoResuelta(): string
    {
        return $this->sortDirection === 'asc' ? 'asc' : 'desc';
    }

    private function porPaginaResuelta(): int
    {
        return in_array($this->perPage, self::PER_PAGE_OPTIONS, true) ? $this->perPage : 15;
    }
}
