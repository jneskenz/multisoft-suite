<?php

namespace Modules\ERP\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Modules\ERP\Models\Receta;
use Modules\ERP\Models\RecetaContactologia;
use Modules\ERP\Models\RecetaGraduacion;
use Modules\ERP\Models\RecetaLentesEnUso;
use Modules\ERP\Models\RecetaOftalmica;

class RecetaDetalleForm extends Component
{
    public int $recetaId;
    public string $tabActiva = 'oftalmologia';
    public array $recetaResumen = [];
    public array $oftalmica = [];
    public array $graduacion = [];
    public array $contactologia = [];
    public array $lentesEnUso = [];
    public array $autorefractometroOd = [];
    public array $autorefractometroOi = [];
    public bool $tieneTablaOftalmica = false;
    public bool $tieneTablaGraduacion = false;
    public bool $tieneTablaContactologia = false;
    public bool $tieneTablaLentesEnUso = false;

    public function mount(int $recetaId): void
    {
        $this->recetaId = $recetaId;

        $this->tieneTablaOftalmica = Schema::hasTable('erp_receta_oftalmicas');
        $this->tieneTablaGraduacion = Schema::hasTable('erp_receta_graduaciones');
        $this->tieneTablaContactologia = Schema::hasTable('erp_receta_contactologia');
        $this->tieneTablaLentesEnUso = Schema::hasTable('erp_receta_lentes_en_uso');

        $receta = $this->obtenerRecetaConContexto();
        $this->asegurarDetallesBase($receta);
        $this->cargarDetalle($receta->fresh([
            'ticket:id,ticket_numero',
            'paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento',
            'especialista:id,name',
            'oftalmica',
            'graduacion',
            'contactologia',
            'lentesEnUso',
        ]));
    }

    public function seleccionarTab(string $tab): void
    {
        $tabsDisponibles = collect($this->tabsDisponibles())->pluck('key')->all();

        if (in_array($tab, $tabsDisponibles, true)) {
            $this->tabActiva = $tab;
        }
    }

    public function guardarOftalmologia(): void
    {
        if (!$this->tieneTablaOftalmica) {
            return;
        }

        $data = $this->validate($this->reglasOftalmologia())['oftalmica'];
        $detalle = $this->obtenerRecetaConContexto()->oftalmica ?: new RecetaOftalmica(['receta_id' => $this->recetaId]);

        $detalle->fill($data);
        $detalle->updated_by = auth()->id();
        if (!$detalle->exists) {
            $detalle->created_by = auth()->id();
        }
        $detalle->save();

        session()->flash('status', 'Oftalmologia guardada correctamente.');
    }

    public function guardarGraduacion(): void
    {
        if (!$this->tieneTablaGraduacion) {
            return;
        }

        $validated = $this->validate($this->reglasGraduacion());
        $data = $validated['graduacion'];
        $data['autorefractometro_od_json'] = $this->normalizarAutorefractometroParaGuardar($validated['autorefractometroOd'] ?? []);
        $data['autorefractometro_oi_json'] = $this->normalizarAutorefractometroParaGuardar($validated['autorefractometroOi'] ?? []);

        $detalle = $this->obtenerRecetaConContexto()->graduacion ?: new RecetaGraduacion(['receta_id' => $this->recetaId]);

        $detalle->fill($data);
        $detalle->updated_by = auth()->id();
        if (!$detalle->exists) {
            $detalle->created_by = auth()->id();
            $detalle->estado = 1;
        }
        $detalle->save();

        session()->flash('status', 'Graduacion guardada correctamente.');
    }

    public function guardarContactologia(): void
    {
        if (!$this->tieneTablaContactologia) {
            return;
        }

        $data = $this->validate($this->reglasContactologia())['contactologia'];
        $detalle = $this->obtenerRecetaConContexto()->contactologia ?: new RecetaContactologia(['receta_id' => $this->recetaId]);

        $detalle->fill($data);
        $detalle->updated_by = auth()->id();
        if (!$detalle->exists) {
            $detalle->created_by = auth()->id();
            $detalle->estado = 1;
        }
        $detalle->save();

        session()->flash('status', 'Contactologia guardada correctamente.');
    }

    public function guardarLentesEnUso(): void
    {
        if (!$this->tieneTablaLentesEnUso) {
            return;
        }

        $data = $this->validate($this->reglasLentesEnUso())['lentesEnUso'];
        $detalle = $this->obtenerRecetaConContexto()->lentesEnUso ?: new RecetaLentesEnUso(['receta_id' => $this->recetaId]);

        $detalle->fill($data);
        $detalle->updated_by = auth()->id();
        if (!$detalle->exists) {
            $detalle->created_by = auth()->id();
            $detalle->estado = 1;
        }
        $detalle->save();

        session()->flash('status', 'Lentes en uso guardado correctamente.');
    }

    public function render()
    {
        return view('erp::livewire.receta-detalle-form', [
            'tabs' => $this->tabsDisponibles(),
        ]);
    }

    private function cargarDetalle(Receta $receta): void
    {
        $pacienteNombre = trim((string) ($receta->paciente?->nombre_completo ?: implode(' ', array_filter([
            $receta->paciente?->nombres,
            $receta->paciente?->apellido_paterno,
            $receta->paciente?->apellido_materno,
        ]))));
        $pacienteDocumento = trim(implode(' ', array_filter([
            $receta->paciente?->tipo_documento,
            $receta->paciente?->numero_documento,
        ])));

        $this->recetaResumen = [
            'numero' => (string) $receta->receta_numero,
            'fecha' => optional($receta->fecha_receta)->format('Y-m-d H:i'),
            'paciente' => $pacienteNombre !== '' ? $pacienteNombre : 'Sin paciente',
            'documento' => $pacienteDocumento,
            'ticket' => (string) ($receta->ticket?->ticket_numero ?? '-'),
            'especialista' => (string) ($receta->especialista?->name ?? '-'),
            'estado' => (string) $receta->estado_receta,
        ];

        $this->oftalmica = array_merge(
            $this->oftalmologiaPorDefecto(),
            $receta->oftalmica?->only(array_keys($this->oftalmologiaPorDefecto())) ?? []
        );

        $this->graduacion = array_merge(
            $this->graduacionPorDefecto(),
            $receta->graduacion?->only(array_keys($this->graduacionPorDefecto())) ?? []
        );

        $this->autorefractometroOd = $this->normalizarAutorefractometro($receta->graduacion?->autorefractometro_od_json);
        $this->autorefractometroOi = $this->normalizarAutorefractometro($receta->graduacion?->autorefractometro_oi_json);

        $this->contactologia = array_merge(
            $this->contactologiaPorDefecto(),
            $receta->contactologia?->only(array_keys($this->contactologiaPorDefecto())) ?? []
        );

        $this->lentesEnUso = array_merge(
            $this->lentesEnUsoPorDefecto(),
            $receta->lentesEnUso?->only(array_keys($this->lentesEnUsoPorDefecto())) ?? []
        );

        $this->lentesEnUso['usa_lejos'] = (bool) ($this->lentesEnUso['usa_lejos'] ?? false);
        $this->lentesEnUso['usa_cerca'] = (bool) ($this->lentesEnUso['usa_cerca'] ?? false);
    }

    private function tabsDisponibles(): array
    {
        return array_values(array_filter([
            ['key' => 'oftalmologia', 'label' => 'Oftalmologia', 'enabled' => $this->tieneTablaOftalmica],
            ['key' => 'graduacion', 'label' => 'Graduacion', 'enabled' => $this->tieneTablaGraduacion],
            ['key' => 'contactologia', 'label' => 'Contactologia', 'enabled' => $this->tieneTablaContactologia],
            ['key' => 'lentes_en_uso', 'label' => 'Lentes en uso', 'enabled' => $this->tieneTablaLentesEnUso],
        ], fn(array $tab): bool => $tab['enabled']));
    }

    private function reglasOftalmologia(): array
    {
        $rules = [];

        foreach (['av_sc_od', 'av_sc_oi', 'av_cc_od', 'av_cc_oi', 'av_ae_od', 'av_ae_oi'] as $campo) {
            $rules["oftalmica.$campo"] = ['nullable', 'string', 'max:30'];
        }

        foreach (['tonometria_od', 'tonometria_oi'] as $campo) {
            $rules["oftalmica.$campo"] = ['nullable', 'numeric', 'between:0,999.99'];
        }

        foreach (['fondo_ojo_od', 'fondo_ojo_oi', 'diagnostico_od', 'diagnostico_oi', 'tratamiento_od', 'tratamiento_oi'] as $campo) {
            $rules["oftalmica.$campo"] = ['nullable', 'string', 'max:120'];
        }

        foreach ([
            'anamnesis',
            'antecedentes_personales',
            'antecedentes_familiares',
            'antecedentes_quirurgicos',
            'biomicroscopia_od',
            'biomicroscopia_oi',
            'diagnostico_od_observacion',
            'diagnostico_oi_observacion',
            'tratamiento_od_observacion',
            'tratamiento_oi_observacion',
        ] as $campo) {
            $rules["oftalmica.$campo"] = ['nullable', 'string', 'max:4000'];
        }

        return $rules;
    }

    private function reglasGraduacion(): array
    {
        $rules = [];

        foreach ([
            'lejos_od_esferico', 'lejos_od_cilindro', 'lejos_oi_esferico', 'lejos_oi_cilindro',
            'adicion_cerca_od', 'adicion_cerca_oi', 'adicion_intermedia_od', 'adicion_intermedia_oi',
            'cerca_od_esferico', 'cerca_od_cilindro', 'cerca_oi_esferico', 'cerca_oi_cilindro',
            'intermedia_od_esferico', 'intermedia_od_cilindro', 'intermedia_oi_esferico', 'intermedia_oi_cilindro',
        ] as $campo) {
            $rules["graduacion.$campo"] = ['nullable', 'numeric', 'between:-999.99,999.99'];
        }

        foreach (['lejos_od_eje', 'lejos_oi_eje', 'cerca_od_eje', 'cerca_oi_eje', 'intermedia_od_eje', 'intermedia_oi_eje'] as $campo) {
            $rules["graduacion.$campo"] = ['nullable', 'integer', 'between:0,180'];
        }

        foreach ([
            'lejos_od_av', 'lejos_od_prisma', 'lejos_od_base', 'lejos_od_dnp',
            'lejos_oi_av', 'lejos_oi_prisma', 'lejos_oi_base', 'lejos_oi_dnp',
            'cerca_od_av', 'cerca_od_prisma', 'cerca_od_base', 'cerca_od_dnp',
            'cerca_oi_av', 'cerca_oi_prisma', 'cerca_oi_base', 'cerca_oi_dnp',
            'intermedia_od_av', 'intermedia_od_prisma', 'intermedia_od_base', 'intermedia_od_dnp',
            'intermedia_oi_av', 'intermedia_oi_prisma', 'intermedia_oi_base', 'intermedia_oi_dnp',
            'autorefractometro_ticket_numero', 'autorefractometro_distancia_pupilar',
        ] as $campo) {
            $rules["graduacion.$campo"] = ['nullable', 'string', 'max:50'];
        }

        foreach (['lejos_dip', 'cerca_dip', 'intermedia_dip', 'recomendaciones'] as $campo) {
            $rules["graduacion.$campo"] = ['nullable', 'string', 'max:4000'];
        }

        $rules['graduacion.fecha_cita'] = ['nullable', 'date'];
        $rules['graduacion.fecha_proxima_cita'] = ['nullable', 'date'];

        $rules['autorefractometroOd.equivalente_esferico'] = ['nullable', 'string', 'max:30'];
        $rules['autorefractometroOi.equivalente_esferico'] = ['nullable', 'string', 'max:30'];
        $rules['autorefractometroOd.lecturas.*.esferico'] = ['nullable', 'string', 'max:30'];
        $rules['autorefractometroOd.lecturas.*.cilindro'] = ['nullable', 'string', 'max:30'];
        $rules['autorefractometroOd.lecturas.*.eje'] = ['nullable', 'string', 'max:30'];
        $rules['autorefractometroOi.lecturas.*.esferico'] = ['nullable', 'string', 'max:30'];
        $rules['autorefractometroOi.lecturas.*.cilindro'] = ['nullable', 'string', 'max:30'];
        $rules['autorefractometroOi.lecturas.*.eje'] = ['nullable', 'string', 'max:30'];

        return $rules;
    }

    private function reglasContactologia(): array
    {
        $rules = [];

        foreach ([
            'queratometria_od_horizontal',
            'queratometria_od_vertical',
            'queratometria_oi_horizontal',
            'queratometria_oi_vertical',
            'shirmer_od',
            'shirmer_oi',
            'but_od',
            'but_oi',
        ] as $campo) {
            $rules["contactologia.$campo"] = ['nullable', 'string', 'max:30'];
        }

        foreach (['queratometria_od_eje', 'queratometria_oi_eje', 'prueba_od_eje', 'prueba_oi_eje', 'definitivo_od_eje', 'definitivo_oi_eje', 'sobrerefraccion_od_eje', 'sobrerefraccion_oi_eje'] as $campo) {
            $rules["contactologia.$campo"] = ['nullable', 'integer', 'between:0,180'];
        }

        foreach ([
            'prueba_od_esferico', 'prueba_od_cilindro', 'prueba_od_cb', 'prueba_od_diametro',
            'prueba_oi_esferico', 'prueba_oi_cilindro', 'prueba_oi_cb', 'prueba_oi_diametro',
            'definitivo_od_esferico', 'definitivo_od_cilindro', 'definitivo_od_cb', 'definitivo_od_diametro',
            'definitivo_oi_esferico', 'definitivo_oi_cilindro', 'definitivo_oi_cb', 'definitivo_oi_diametro',
            'sobrerefraccion_od_esferico', 'sobrerefraccion_od_cilindro',
            'sobrerefraccion_oi_esferico', 'sobrerefraccion_oi_cilindro',
        ] as $campo) {
            $rules["contactologia.$campo"] = ['nullable', 'numeric', 'between:-999.99,999.99'];
        }

        foreach (['sobrerefraccion_od_giro', 'sobrerefraccion_oi_giro'] as $campo) {
            $rules["contactologia.$campo"] = ['nullable', 'string', 'max:20'];
        }

        foreach (['material', 'tipo_uso', 'marca'] as $campo) {
            $rules["contactologia.$campo"] = ['nullable', 'string', 'max:120'];
        }

        return $rules;
    }

    private function reglasLentesEnUso(): array
    {
        $rules = [];

        foreach (['od_esferico', 'od_cilindro', 'od_adicion', 'oi_esferico', 'oi_cilindro', 'oi_adicion'] as $campo) {
            $rules["lentesEnUso.$campo"] = ['nullable', 'numeric', 'between:-999.99,999.99'];
        }

        foreach (['od_eje', 'oi_eje'] as $campo) {
            $rules["lentesEnUso.$campo"] = ['nullable', 'integer', 'between:0,180'];
        }

        foreach (['od_av_cc', 'od_altura', 'oi_av_cc', 'oi_altura'] as $campo) {
            $rules["lentesEnUso.$campo"] = ['nullable', 'string', 'max:30'];
        }

        $rules['lentesEnUso.dip'] = ['nullable', 'string', 'max:4000'];
        $rules['lentesEnUso.observaciones'] = ['nullable', 'string', 'max:4000'];
        $rules['lentesEnUso.usa_lejos'] = ['boolean'];
        $rules['lentesEnUso.usa_cerca'] = ['boolean'];

        return $rules;
    }

    private function asegurarDetallesBase(Receta $receta): void
    {
        if ($this->tieneTablaOftalmica && !$receta->oftalmica()->exists()) {
            RecetaOftalmica::query()->create([
                'receta_id' => $receta->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        if ($this->tieneTablaGraduacion && !$receta->graduacion()->exists()) {
            RecetaGraduacion::query()->create([
                'receta_id' => $receta->id,
                'estado' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        if ($this->tieneTablaContactologia && !$receta->contactologia()->exists()) {
            RecetaContactologia::query()->create([
                'receta_id' => $receta->id,
                'estado' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        if ($this->tieneTablaLentesEnUso && !$receta->lentesEnUso()->exists()) {
            RecetaLentesEnUso::query()->create([
                'receta_id' => $receta->id,
                'estado' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
    }

    private function obtenerRecetaConContexto(): Receta
    {
        [$tenantId, $groupCompanyId] = $this->contextoActual();

        return Receta::query()
            ->with([
                'ticket:id,ticket_numero',
                'paciente:id,nombre_completo,nombres,apellido_paterno,apellido_materno,tipo_documento,numero_documento',
                'especialista:id,name',
                'oftalmica',
                'graduacion',
                'contactologia',
                'lentesEnUso',
            ])
            ->tenant($tenantId)
            ->when($groupCompanyId !== null, function (Builder $query) use ($groupCompanyId): void {
                $query->where(function (Builder $scope) use ($groupCompanyId): void {
                    $scope->where('group_company_id', $groupCompanyId)
                        ->orWhereNull('group_company_id');
                });
            })
            ->findOrFail($this->recetaId);
    }

    private function contextoActual(): array
    {
        $group = current_group();
        $tenantId = (int) ($group?->tenant_id ?? auth()->user()?->tenant_id ?? 0);
        $groupCompanyId = $group?->id ? (int) $group->id : null;

        abort_if($tenantId <= 0, 403, 'No hay contexto de tenant activo.');

        return [$tenantId, $groupCompanyId];
    }

    private function oftalmologiaPorDefecto(): array
    {
        return [
            'av_sc_od' => '',
            'av_sc_oi' => '',
            'av_cc_od' => '',
            'av_cc_oi' => '',
            'av_ae_od' => '',
            'av_ae_oi' => '',
            'tonometria_od' => '',
            'tonometria_oi' => '',
            'fondo_ojo_od' => '',
            'fondo_ojo_oi' => '',
            'anamnesis' => '',
            'antecedentes_personales' => '',
            'antecedentes_familiares' => '',
            'antecedentes_quirurgicos' => '',
            'biomicroscopia_od' => '',
            'biomicroscopia_oi' => '',
            'diagnostico_od' => '',
            'diagnostico_od_observacion' => '',
            'diagnostico_oi' => '',
            'diagnostico_oi_observacion' => '',
            'tratamiento_od' => '',
            'tratamiento_od_observacion' => '',
            'tratamiento_oi' => '',
            'tratamiento_oi_observacion' => '',
        ];
    }

    private function graduacionPorDefecto(): array
    {
        return [
            'lejos_od_esferico' => '',
            'lejos_od_cilindro' => '',
            'lejos_od_eje' => '',
            'lejos_od_av' => '',
            'lejos_od_prisma' => '',
            'lejos_od_base' => '',
            'lejos_od_dnp' => '',
            'lejos_oi_esferico' => '',
            'lejos_oi_cilindro' => '',
            'lejos_oi_eje' => '',
            'lejos_oi_av' => '',
            'lejos_oi_prisma' => '',
            'lejos_oi_base' => '',
            'lejos_oi_dnp' => '',
            'lejos_dip' => '',
            'adicion_cerca_od' => '',
            'adicion_cerca_oi' => '',
            'adicion_intermedia_od' => '',
            'adicion_intermedia_oi' => '',
            'autorefractometro_ticket_numero' => '',
            'autorefractometro_distancia_pupilar' => '',
            'cerca_od_esferico' => '',
            'cerca_od_cilindro' => '',
            'cerca_od_eje' => '',
            'cerca_od_av' => '',
            'cerca_od_prisma' => '',
            'cerca_od_base' => '',
            'cerca_od_dnp' => '',
            'cerca_oi_esferico' => '',
            'cerca_oi_cilindro' => '',
            'cerca_oi_eje' => '',
            'cerca_oi_av' => '',
            'cerca_oi_prisma' => '',
            'cerca_oi_base' => '',
            'cerca_oi_dnp' => '',
            'cerca_dip' => '',
            'intermedia_od_esferico' => '',
            'intermedia_od_cilindro' => '',
            'intermedia_od_eje' => '',
            'intermedia_od_av' => '',
            'intermedia_od_prisma' => '',
            'intermedia_od_base' => '',
            'intermedia_od_dnp' => '',
            'intermedia_oi_esferico' => '',
            'intermedia_oi_cilindro' => '',
            'intermedia_oi_eje' => '',
            'intermedia_oi_av' => '',
            'intermedia_oi_prisma' => '',
            'intermedia_oi_base' => '',
            'intermedia_oi_dnp' => '',
            'intermedia_dip' => '',
            'fecha_cita' => '',
            'fecha_proxima_cita' => '',
            'recomendaciones' => '',
        ];
    }

    private function contactologiaPorDefecto(): array
    {
        return [
            'queratometria_od_horizontal' => '',
            'queratometria_od_vertical' => '',
            'queratometria_od_eje' => '',
            'queratometria_oi_horizontal' => '',
            'queratometria_oi_vertical' => '',
            'queratometria_oi_eje' => '',
            'prueba_od_esferico' => '',
            'prueba_od_cilindro' => '',
            'prueba_od_eje' => '',
            'prueba_od_cb' => '',
            'prueba_od_diametro' => '',
            'prueba_oi_esferico' => '',
            'prueba_oi_cilindro' => '',
            'prueba_oi_eje' => '',
            'prueba_oi_cb' => '',
            'prueba_oi_diametro' => '',
            'definitivo_od_esferico' => '',
            'definitivo_od_cilindro' => '',
            'definitivo_od_eje' => '',
            'definitivo_od_cb' => '',
            'definitivo_od_diametro' => '',
            'definitivo_oi_esferico' => '',
            'definitivo_oi_cilindro' => '',
            'definitivo_oi_eje' => '',
            'definitivo_oi_cb' => '',
            'definitivo_oi_diametro' => '',
            'sobrerefraccion_od_esferico' => '',
            'sobrerefraccion_od_cilindro' => '',
            'sobrerefraccion_od_eje' => '',
            'sobrerefraccion_od_giro' => '',
            'sobrerefraccion_oi_esferico' => '',
            'sobrerefraccion_oi_cilindro' => '',
            'sobrerefraccion_oi_eje' => '',
            'sobrerefraccion_oi_giro' => '',
            'material' => '',
            'tipo_uso' => '',
            'marca' => '',
            'shirmer_od' => '',
            'shirmer_oi' => '',
            'but_od' => '',
            'but_oi' => '',
        ];
    }

    private function lentesEnUsoPorDefecto(): array
    {
        return [
            'od_esferico' => '',
            'od_cilindro' => '',
            'od_eje' => '',
            'od_av_cc' => '',
            'od_altura' => '',
            'od_adicion' => '',
            'oi_esferico' => '',
            'oi_cilindro' => '',
            'oi_eje' => '',
            'oi_av_cc' => '',
            'oi_altura' => '',
            'oi_adicion' => '',
            'dip' => '',
            'usa_lejos' => false,
            'usa_cerca' => false,
            'observaciones' => '',
        ];
    }

    private function autorefractometroPorDefecto(): array
    {
        return [
            'equivalente_esferico' => '',
            'lecturas' => [
                ['esferico' => '', 'cilindro' => '', 'eje' => ''],
                ['esferico' => '', 'cilindro' => '', 'eje' => ''],
                ['esferico' => '', 'cilindro' => '', 'eje' => ''],
                ['esferico' => '', 'cilindro' => '', 'eje' => ''],
            ],
        ];
    }

    private function normalizarAutorefractometro(mixed $valor): array
    {
        $base = $this->autorefractometroPorDefecto();

        if (!is_array($valor)) {
            return $base;
        }

        $base['equivalente_esferico'] = (string) ($valor['equivalente_esferico'] ?? '');
        $lecturas = is_array($valor['lecturas'] ?? null) ? array_values($valor['lecturas']) : [];

        for ($i = 0; $i < 4; $i++) {
            $fila = is_array($lecturas[$i] ?? null) ? $lecturas[$i] : [];
            $base['lecturas'][$i] = [
                'esferico' => (string) ($fila['esferico'] ?? ''),
                'cilindro' => (string) ($fila['cilindro'] ?? ''),
                'eje' => (string) ($fila['eje'] ?? ''),
            ];
        }

        return $base;
    }

    private function normalizarAutorefractometroParaGuardar(array $valor): ?array
    {
        $equivalente = trim((string) ($valor['equivalente_esferico'] ?? ''));
        $lecturas = [];
        $tieneDatos = $equivalente !== '';

        foreach (($valor['lecturas'] ?? []) as $fila) {
            $lectura = [
                'esferico' => trim((string) ($fila['esferico'] ?? '')),
                'cilindro' => trim((string) ($fila['cilindro'] ?? '')),
                'eje' => trim((string) ($fila['eje'] ?? '')),
            ];

            if ($lectura['esferico'] !== '' || $lectura['cilindro'] !== '' || $lectura['eje'] !== '') {
                $tieneDatos = true;
            }

            $lecturas[] = $lectura;
        }

        if (!$tieneDatos) {
            return null;
        }

        return [
            'equivalente_esferico' => $equivalente,
            'lecturas' => $lecturas,
        ];
    }
}
