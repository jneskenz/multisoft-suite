<?php

namespace Modules\Partners\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Modules\Partners\Models\Empresa;
use Modules\Partners\Models\Persona;
use Modules\Partners\Models\PersonaEmpresa;
use Modules\Partners\Models\TipoPersona;

class PartnerDirectoryController extends BaseController
{
    private const TIPOS_PERSONA = ['cliente', 'proveedor', 'paciente'];

    private const TIPOS_RELACION = [
        'contacto',
        'titular',
        'representante_legal',
        'empleado',
        'apoderado',
    ];

    public function personas()
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        $base = $this->personasBaseQuery(tenantId: $tenantId, groupCompanyId: $groupCompanyId);
        $personas = $base instanceof Collection ? $base : $base->get();

        return view('partners::personas.index', [
            'personas' => $personas,
        ]);
    }

    public function createPersona(Request $request)
    {
        $tipoPreseleccionado = trim((string) $request->query('tipo', ''));
        $tiposSeleccionados = in_array($tipoPreseleccionado, self::TIPOS_PERSONA, true)
            ? [$tipoPreseleccionado]
            : [];

        return view('partners::personas.create', [
            'persona' => new Persona(),
            'tiposDisponibles' => self::TIPOS_PERSONA,
            'tiposSeleccionados' => $tiposSeleccionados,
        ]);
    }

    public function storePersona(Request $request): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        $validated = $this->validatePersona($request, $tenantId);

        DB::transaction(function () use ($validated, $tenantId, $groupCompanyId): void {
            $persona = Persona::create([
                'tenant_id' => $tenantId,
                'group_company_id' => $groupCompanyId,
                'tipo_documento' => $validated['tipo_documento'] ?? null,
                'numero_documento' => $validated['numero_documento'] ?? null,
                'nombres' => $validated['nombres'],
                'apellido_paterno' => $validated['apellido_paterno'] ?? null,
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'nombre_completo' => $this->buildNombreCompleto(
                    $validated['nombre_completo'] ?? null,
                    $validated['nombres'],
                    $validated['apellido_paterno'] ?? null,
                    $validated['apellido_materno'] ?? null,
                ),
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                'estado' => (bool) $validated['estado'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $this->syncTiposPersona($persona, $validated['tipos'] ?? []);
        });

        return redirect()->route('partners.personas.index')
            ->with('status', 'Persona creada correctamente.');
    }

    public function editPersona(Persona $persona)
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertPersonaInContext($persona, $tenantId, $groupCompanyId);

        $tiposSeleccionados = Schema::hasTable('partners_tipo_personas')
            ? $persona->tipos()->pluck('tipo')->toArray()
            : [];

        return view('partners::personas.edit', [
            'persona' => $persona,
            'tiposDisponibles' => self::TIPOS_PERSONA,
            'tiposSeleccionados' => $tiposSeleccionados,
        ]);
    }

    public function updatePersona(Request $request, Persona $persona): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertPersonaInContext($persona, $tenantId, $groupCompanyId);

        $validated = $this->validatePersona($request, $tenantId, $persona);

        DB::transaction(function () use ($persona, $validated): void {
            $persona->update([
                'tipo_documento' => $validated['tipo_documento'] ?? null,
                'numero_documento' => $validated['numero_documento'] ?? null,
                'nombres' => $validated['nombres'],
                'apellido_paterno' => $validated['apellido_paterno'] ?? null,
                'apellido_materno' => $validated['apellido_materno'] ?? null,
                'nombre_completo' => $this->buildNombreCompleto(
                    $validated['nombre_completo'] ?? null,
                    $validated['nombres'],
                    $validated['apellido_paterno'] ?? null,
                    $validated['apellido_materno'] ?? null,
                ),
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                'estado' => (bool) $validated['estado'],
                'updated_by' => auth()->id(),
            ]);

            $this->syncTiposPersona($persona, $validated['tipos'] ?? []);
        });

        return redirect()->route('partners.personas.index')
            ->with('status', 'Persona actualizada correctamente.');
    }

    public function destroyPersona(Persona $persona): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertPersonaInContext($persona, $tenantId, $groupCompanyId);

        DB::transaction(function () use ($persona): void {
            if (Schema::hasTable('partners_tipo_personas')) {
                $persona->tipos()->delete();
            }

            if (Schema::hasTable('partners_persona_empresa')) {
                $persona->relacionesEmpresas()->delete();
            }

            $persona->delete();
        });

        return redirect()->route('partners.personas.index')
            ->with('status', 'Persona eliminada correctamente.');
    }

    public function empresas()
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        if (!Schema::hasTable('partners_empresas')) {
            return view('partners::empresas.index', ['empresas' => collect()]);
        }

        $query = Empresa::query()
            ->select(['id', 'tenant_id', 'group_company_id', 'ruc', 'razon_social', 'nombre_comercial', 'email', 'telefono', 'estado', 'created_at'])
            ->where('tenant_id', $tenantId)
            ->orderBy('razon_social');

        if ($groupCompanyId !== null) {
            $query->where(function (Builder $q) use ($groupCompanyId): void {
                $q->where('group_company_id', $groupCompanyId)
                    ->orWhereNull('group_company_id');
            });
        }

        if (Schema::hasTable('partners_persona_empresa')) {
            $query->withCount('personas');
        }

        $empresas = $query->get();

        return view('partners::empresas.index', [
            'empresas' => $empresas,
        ]);
    }

    public function createEmpresa()
    {
        return view('partners::empresas.create', [
            'empresa' => new Empresa(),
        ]);
    }

    public function storeEmpresa(Request $request): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $validated = $this->validateEmpresa($request, $tenantId);

        Empresa::create([
            'tenant_id' => $tenantId,
            'group_company_id' => $groupCompanyId,
            'ruc' => $validated['ruc'],
            'razon_social' => $validated['razon_social'],
            'nombre_comercial' => $validated['nombre_comercial'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'email' => $validated['email'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'estado' => (bool) $validated['estado'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('partners.empresas.index')
            ->with('status', 'Empresa creada correctamente.');
    }

    public function editEmpresa(Empresa $empresa)
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertEmpresaInContext($empresa, $tenantId, $groupCompanyId);

        return view('partners::empresas.edit', [
            'empresa' => $empresa,
        ]);
    }

    public function updateEmpresa(Request $request, Empresa $empresa): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertEmpresaInContext($empresa, $tenantId, $groupCompanyId);

        $validated = $this->validateEmpresa($request, $tenantId, $empresa);

        $empresa->update([
            'ruc' => $validated['ruc'],
            'razon_social' => $validated['razon_social'],
            'nombre_comercial' => $validated['nombre_comercial'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'email' => $validated['email'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'estado' => (bool) $validated['estado'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('partners.empresas.index')
            ->with('status', 'Empresa actualizada correctamente.');
    }

    public function destroyEmpresa(Empresa $empresa): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertEmpresaInContext($empresa, $tenantId, $groupCompanyId);

        DB::transaction(function () use ($empresa): void {
            if (Schema::hasTable('partners_persona_empresa')) {
                $empresa->relacionesPersonas()->delete();
            }

            $empresa->delete();
        });

        return redirect()->route('partners.empresas.index')
            ->with('status', 'Empresa eliminada correctamente.');
    }

    public function relaciones()
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        if (
            !Schema::hasTable('partners_persona_empresa')
            || !Schema::hasTable('partners_personas')
            || !Schema::hasTable('partners_empresas')
        ) {
            return view('partners::relaciones.index', ['relaciones' => collect()]);
        }

        $relaciones = PersonaEmpresa::query()
            ->whereHas('persona', function (Builder $query) use ($tenantId, $groupCompanyId): void {
                $this->applyContextScope($query, $tenantId, $groupCompanyId);
            })
            ->with([
                'persona:id,tenant_id,group_company_id,nombres,apellido_paterno,apellido_materno,nombre_completo',
                'empresa:id,tenant_id,group_company_id,ruc,razon_social,nombre_comercial',
            ])
            ->orderByDesc('id')
            ->get();

        return view('partners::relaciones.index', [
            'relaciones' => $relaciones,
        ]);
    }

    public function createRelacion()
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        return view('partners::relaciones.create', [
            'relacion' => new PersonaEmpresa(),
            'personas' => $this->personasForSelect($tenantId, $groupCompanyId),
            'empresas' => $this->empresasForSelect($tenantId, $groupCompanyId),
            'tiposRelacion' => self::TIPOS_RELACION,
        ]);
    }

    public function storeRelacion(Request $request): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $validated = $this->validateRelacion($request, $tenantId, $groupCompanyId);

        DB::transaction(function () use ($validated): void {
            if ((bool) $validated['es_principal']) {
                $this->clearPrincipalForPersona((int) $validated['persona_id']);
            }

            PersonaEmpresa::create([
                'persona_id' => (int) $validated['persona_id'],
                'empresa_id' => (int) $validated['empresa_id'],
                'tipo_relacion' => $validated['tipo_relacion'],
                'es_principal' => (bool) $validated['es_principal'],
                'estado' => (bool) $validated['estado'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('partners.relaciones.index')
            ->with('status', 'Relacion creada correctamente.');
    }

    public function editRelacion(PersonaEmpresa $relacion)
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertRelacionInContext($relacion, $tenantId, $groupCompanyId);

        return view('partners::relaciones.edit', [
            'relacion' => $relacion,
            'personas' => $this->personasForSelect($tenantId, $groupCompanyId),
            'empresas' => $this->empresasForSelect($tenantId, $groupCompanyId),
            'tiposRelacion' => self::TIPOS_RELACION,
        ]);
    }

    public function updateRelacion(Request $request, PersonaEmpresa $relacion): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertRelacionInContext($relacion, $tenantId, $groupCompanyId);

        $validated = $this->validateRelacion($request, $tenantId, $groupCompanyId, $relacion);

        DB::transaction(function () use ($relacion, $validated): void {
            if ((bool) $validated['es_principal']) {
                $this->clearPrincipalForPersona((int) $validated['persona_id'], $relacion->id);
            }

            $relacion->update([
                'persona_id' => (int) $validated['persona_id'],
                'empresa_id' => (int) $validated['empresa_id'],
                'tipo_relacion' => $validated['tipo_relacion'],
                'es_principal' => (bool) $validated['es_principal'],
                'estado' => (bool) $validated['estado'],
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('partners.relaciones.index')
            ->with('status', 'Relacion actualizada correctamente.');
    }

    public function destroyRelacion(PersonaEmpresa $relacion): RedirectResponse
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();
        $this->assertRelacionInContext($relacion, $tenantId, $groupCompanyId);

        $relacion->delete();

        return redirect()->route('partners.relaciones.index')
            ->with('status', 'Relacion eliminada correctamente.');
    }

    public function clientes()
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        return $this->renderPersonasByTipo(
            tipo: 'cliente',
            titulo: 'Clientes',
            descripcion: 'Vista filtrada de personas con tipo cliente',
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
        );
    }

    public function proveedores()
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        return $this->renderPersonasByTipo(
            tipo: 'proveedor',
            titulo: 'Proveedores',
            descripcion: 'Vista filtrada de personas con tipo proveedor',
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
        );
    }

    public function pacientes()
    {
        [$tenantId, $groupCompanyId] = $this->currentContext();

        return $this->renderPersonasByTipo(
            tipo: 'paciente',
            titulo: 'Pacientes',
            descripcion: 'Vista filtrada de personas con tipo paciente',
            tenantId: $tenantId,
            groupCompanyId: $groupCompanyId,
        );
    }

    private function renderPersonasByTipo(string $tipo, string $titulo, string $descripcion, int $tenantId, ?int $groupCompanyId)
    {
        $base = $this->personasBaseQuery(tipo: $tipo, tenantId: $tenantId, groupCompanyId: $groupCompanyId);
        $personas = $base instanceof Collection ? $base : $base->get();

        return view('partners::personas.tipo', [
            'personas' => $personas,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
        ]);
    }

    private function personasBaseQuery(?string $tipo = null, ?int $tenantId = null, ?int $groupCompanyId = null)
    {
        if (!Schema::hasTable('partners_personas')) {
            return collect();
        }

        [$resolvedTenantId, $resolvedGroupCompanyId] = $tenantId === null
            ? $this->currentContext()
            : [$tenantId, $groupCompanyId];

        $query = Persona::query()
            ->select([
                'id',
                'tenant_id',
                'group_company_id',
                'tipo_documento',
                'numero_documento',
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'nombre_completo',
                'email',
                'telefono',
                'estado',
                'created_at',
            ]);

        $this->applyContextScope($query, $resolvedTenantId, $resolvedGroupCompanyId)
            ->orderBy('nombres')
            ->orderBy('apellido_paterno');

        $relations = [];

        if (Schema::hasTable('partners_tipo_personas')) {
            $relations['tipos'] = fn ($relation) => $relation->select('id', 'persona_id', 'tipo', 'estado');
        }

        if (Schema::hasTable('partners_persona_empresa') && Schema::hasTable('partners_empresas')) {
            $relations['empresas'] = fn ($relation) => $relation->select('partners_empresas.id', 'ruc', 'razon_social', 'nombre_comercial');
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        if ($tipo !== null && !Schema::hasTable('partners_tipo_personas')) {
            return collect();
        }

        if ($tipo !== null) {
            $query->conTipo($tipo);
        }

        return $query;
    }

    private function personasForSelect(int $tenantId, ?int $groupCompanyId): Collection
    {
        if (!Schema::hasTable('partners_personas')) {
            return collect();
        }

        $query = Persona::query()
            ->select(['id', 'tenant_id', 'group_company_id', 'nombres', 'apellido_paterno', 'apellido_materno', 'nombre_completo'])
            ->where('estado', true)
            ->orderBy('nombres')
            ->orderBy('apellido_paterno');

        $this->applyContextScope($query, $tenantId, $groupCompanyId);

        return $query->get();
    }

    private function empresasForSelect(int $tenantId, ?int $groupCompanyId): Collection
    {
        if (!Schema::hasTable('partners_empresas')) {
            return collect();
        }

        $query = Empresa::query()
            ->select(['id', 'tenant_id', 'group_company_id', 'ruc', 'razon_social', 'nombre_comercial'])
            ->where('estado', true)
            ->where('tenant_id', $tenantId)
            ->orderBy('razon_social');

        if ($groupCompanyId !== null) {
            $query->where(function (Builder $q) use ($groupCompanyId): void {
                $q->where('group_company_id', $groupCompanyId)
                    ->orWhereNull('group_company_id');
            });
        }

        return $query->get();
    }

    private function currentContext(): array
    {
        $group = current_group();
        $tenantId = (int) ($group?->tenant_id ?? auth()->user()?->tenant_id ?? 0);
        $groupCompanyId = $group?->id ? (int) $group->id : null;

        abort_if($tenantId <= 0, 403, 'No hay contexto de tenant activo.');

        return [$tenantId, $groupCompanyId];
    }

    private function applyContextScope(Builder $query, int $tenantId, ?int $groupCompanyId): Builder
    {
        $query->where('tenant_id', $tenantId);

        if ($groupCompanyId !== null) {
            $query->where(function (Builder $q) use ($groupCompanyId): void {
                $q->where('group_company_id', $groupCompanyId)
                    ->orWhereNull('group_company_id');
            });
        }

        return $query;
    }

    private function assertPersonaInContext(Persona $persona, int $tenantId, ?int $groupCompanyId): void
    {
        abort_if((int) $persona->tenant_id !== $tenantId, 404);

        if ($groupCompanyId !== null && $persona->group_company_id !== null) {
            abort_if((int) $persona->group_company_id !== $groupCompanyId, 404);
        }
    }

    private function assertEmpresaInContext(Empresa $empresa, int $tenantId, ?int $groupCompanyId): void
    {
        abort_if((int) $empresa->tenant_id !== $tenantId, 404);

        if ($groupCompanyId !== null && $empresa->group_company_id !== null) {
            abort_if((int) $empresa->group_company_id !== $groupCompanyId, 404);
        }
    }

    private function assertRelacionInContext(PersonaEmpresa $relacion, int $tenantId, ?int $groupCompanyId): void
    {
        $relacion->loadMissing(['persona', 'empresa']);

        abort_if(!$relacion->persona || !$relacion->empresa, 404);

        $this->assertPersonaInContext($relacion->persona, $tenantId, $groupCompanyId);
        $this->assertEmpresaInContext($relacion->empresa, $tenantId, $groupCompanyId);
    }

    private function validatePersona(Request $request, int $tenantId, ?Persona $persona = null): array
    {
        $docType = trim((string) $request->input('tipo_documento', ''));

        $numeroDocumentoRules = ['nullable', 'string', 'max:30'];

        if (trim((string) $request->input('numero_documento', '')) !== '') {
            $uniqueDocumento = Rule::unique('partners_personas', 'numero_documento')
                ->where(function ($query) use ($tenantId, $docType) {
                    $query->where('tenant_id', $tenantId)
                        ->whereNull('deleted_at');

                    if ($docType !== '') {
                        $query->where('tipo_documento', $docType);
                    }
                });

            if ($persona !== null) {
                $uniqueDocumento = $uniqueDocumento->ignore($persona->id);
            }

            $numeroDocumentoRules[] = $uniqueDocumento;
        }

        return $request->validate([
            'tipo_documento' => ['nullable', 'string', 'max:20'],
            'numero_documento' => $numeroDocumentoRules,
            'nombres' => ['required', 'string', 'max:120'],
            'apellido_paterno' => ['nullable', 'string', 'max:120'],
            'apellido_materno' => ['nullable', 'string', 'max:120'],
            'nombre_completo' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'estado' => ['required', 'boolean'],
            'tipos' => ['nullable', 'array'],
            'tipos.*' => ['string', Rule::in(self::TIPOS_PERSONA)],
        ]);
    }

    private function validateEmpresa(Request $request, int $tenantId, ?Empresa $empresa = null): array
    {
        $rucUnique = Rule::unique('partners_empresas', 'ruc')
            ->where(fn ($query) => $query->where('tenant_id', $tenantId)->whereNull('deleted_at'));

        if ($empresa !== null) {
            $rucUnique = $rucUnique->ignore($empresa->id);
        }

        return $request->validate([
            'ruc' => ['required', 'string', 'max:20', $rucUnique],
            'razon_social' => ['required', 'string', 'max:200'],
            'nombre_comercial' => ['nullable', 'string', 'max:200'],
            'direccion' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', 'boolean'],
        ]);
    }

    private function validateRelacion(Request $request, int $tenantId, ?int $groupCompanyId, ?PersonaEmpresa $relacion = null): array
    {
        $personaExists = Rule::exists('partners_personas', 'id')
            ->where(function ($query) use ($tenantId, $groupCompanyId): void {
                $query->where('tenant_id', $tenantId)
                    ->whereNull('deleted_at');

                if ($groupCompanyId !== null) {
                    $query->where(function ($sub) use ($groupCompanyId): void {
                        $sub->where('group_company_id', $groupCompanyId)
                            ->orWhereNull('group_company_id');
                    });
                }
            });

        $empresaExists = Rule::exists('partners_empresas', 'id')
            ->where(function ($query) use ($tenantId, $groupCompanyId): void {
                $query->where('tenant_id', $tenantId)
                    ->whereNull('deleted_at');

                if ($groupCompanyId !== null) {
                    $query->where(function ($sub) use ($groupCompanyId): void {
                        $sub->where('group_company_id', $groupCompanyId)
                            ->orWhereNull('group_company_id');
                    });
                }
            });

        $uniqueRelacion = Rule::unique('partners_persona_empresa')
            ->where(fn ($query) => $query
                ->where('persona_id', $request->input('persona_id'))
                ->where('empresa_id', $request->input('empresa_id')));

        if ($relacion !== null) {
            $uniqueRelacion = $uniqueRelacion->ignore($relacion->id);
        }

        return $request->validate([
            'persona_id' => ['required', 'integer', $personaExists],
            'empresa_id' => ['required', 'integer', $empresaExists, $uniqueRelacion],
            'tipo_relacion' => ['required', 'string', 'max:40', Rule::in(self::TIPOS_RELACION)],
            'es_principal' => ['required', 'boolean'],
            'estado' => ['required', 'boolean'],
        ]);
    }

    private function syncTiposPersona(Persona $persona, array $tipos): void
    {
        if (!Schema::hasTable('partners_tipo_personas')) {
            return;
        }

        $tiposSanitizados = collect($tipos)
            ->filter(fn ($tipo) => in_array($tipo, self::TIPOS_PERSONA, true))
            ->unique()
            ->values();

        TipoPersona::where('persona_id', $persona->id)->delete();

        foreach ($tiposSanitizados as $tipo) {
            TipoPersona::create([
                'persona_id' => $persona->id,
                'tipo' => $tipo,
                'estado' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
    }

    private function clearPrincipalForPersona(int $personaId, ?int $exceptId = null): void
    {
        $query = PersonaEmpresa::query()->where('persona_id', $personaId);

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        $query->update([
            'es_principal' => false,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);
    }

    private function buildNombreCompleto(?string $provided, string $nombres, ?string $apellidoPaterno, ?string $apellidoMaterno): string
    {
        $provided = trim((string) $provided);
        if ($provided !== '') {
            return $provided;
        }

        return trim(implode(' ', array_filter([
            trim($nombres),
            trim((string) $apellidoPaterno),
            trim((string) $apellidoMaterno),
        ])));
    }
}
