<div>
    @php
        $resumen = $recetaResumen;
        $tabsMeta = $tabs;
        $ojos = ['od' => 'O.D.', 'oi' => 'O.I.'];
        $bloquesGraduacion = [
            'lejos' => 'Vision de lejos',
            'cerca' => 'Vision de cerca',
            'intermedia' => 'Vision intermedia',
        ];
        $camposVision = [
            'esferico' => ['label' => 'Esferico', 'type' => 'number', 'step' => '0.01'],
            'cilindro' => ['label' => 'Cilindro', 'type' => 'number', 'step' => '0.01'],
            'eje' => ['label' => 'Eje', 'type' => 'number'],
            'av' => ['label' => 'A/V', 'type' => 'text'],
            'prisma' => ['label' => 'Prisma', 'type' => 'text'],
            'base' => ['label' => 'Base', 'type' => 'text'],
            'dnp' => ['label' => 'DNP', 'type' => 'text'],
        ];
        $bloquesAv = ['av_sc' => 'A.V S/C', 'av_cc' => 'A.V C/C', 'av_ae' => 'A.V A/E'];
        $contactologiaSecciones = [
            'prueba' => ['titulo' => 'Lente de prueba', 'campos' => ['esferico', 'cilindro', 'eje', 'cb', 'diametro']],
            'definitivo' => [
                'titulo' => 'Lente definitivo',
                'campos' => ['esferico', 'cilindro', 'eje', 'cb', 'diametro'],
            ],
            'sobrerefraccion' => ['titulo' => 'Sobre-refraccion', 'campos' => ['esferico', 'cilindro', 'eje', 'giro']],
        ];
        $metaCamposContacto = [
            'esferico' => ['type' => 'number', 'step' => '0.01'],
            'cilindro' => ['type' => 'number', 'step' => '0.01'],
            'eje' => ['type' => 'number'],
            'cb' => ['type' => 'number', 'step' => '0.01'],
            'diametro' => ['type' => 'number', 'step' => '0.01'],
            'giro' => ['type' => 'text'],
        ];
    @endphp

    <div class="card-body">
        @if (session()->has('status'))
            <div class="alert alert-success alert-dismissible mb-4" role="alert">
                <i class="ti tabler-check me-2"></i>{{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <div class="fw-semibold mb-1">{{ __('Hay errores de validacion.') }}</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="border rounded p-4 mb-4 bg-lighter">
            <div class="row g-3">
                <div class="col-md-3"><small class="text-muted d-block">{{ __('Receta') }}</small>
                    <div class="fw-semibold">{{ $resumen['numero'] ?? '-' }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">{{ __('Paciente') }}</small>
                    <div class="fw-semibold">{{ $resumen['paciente'] ?? '-' }}</div>
                    @if (!empty($resumen['documento']))
                        <small class="text-muted">{{ $resumen['documento'] }}</small>
                    @endif
                </div>
                <div class="col-md-2"><small class="text-muted d-block">{{ __('Ticket') }}</small>
                    <div class="fw-semibold">{{ $resumen['ticket'] ?? '-' }}</div>
                </div>
                <div class="col-md-2"><small class="text-muted d-block">{{ __('Especialista') }}</small>
                    <div class="fw-semibold">{{ $resumen['especialista'] ?? '-' }}</div>
                </div>
                <div class="col-md-2"><small class="text-muted d-block">{{ __('Estado') }}</small><span
                        class="badge bg-label-primary">{{ ucfirst((string) ($resumen['estado'] ?? '-')) }}</span></div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" role="tablist">
            @foreach ($tabsMeta as $tab)
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link {{ $tabActiva === $tab['key'] ? 'active' : '' }}"
                        wire:click="seleccionarTab('{{ $tab['key'] }}')">
                        {{ __($tab['label']) }}
                    </button>
                </li>
            @endforeach
        </ul>

        @if ($tabActiva === 'oftalmologia' && $tieneTablaOftalmica)
            <form wire:submit="guardarOftalmologia">
                <div class="row g-4">
                    <div class="col-5">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0 text-uppercase">{{ __('Agudeza Visual') }}</h6>
                            </div>
                            <div class="card-body py-2 px-2">
                                <div class="row g-3">
                                    @foreach ($bloquesAv as $prefijo => $titulo)
                                        <div class="col-md-4">
                                            <div class="card border shadow-none h-100">
                                                <div class="card-header py-0 px-2 text-center border-bottom">
                                                    <h6 class="mb-0"><small>{{ __($titulo) }}</small></h6>
                                                </div>
                                                <div class="card-body py-1 px-2">
                                                    @foreach ($ojos as $ojo => $label)
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __($label) }}</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model.defer="oftalmica.{{ $prefijo }}_{{ $ojo }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-4">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0 text-uppercase">{{ __('Tonometria') }}</h6>
                            </div>
                            <div class="card-body row g-3 py-2 px-2">
                                <div class="col-6">
                                    <label class="form-label">{{ __('O.D.') }}</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                        wire:model.defer="oftalmica.tonometria_od">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('O.I.') }}</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                        wire:model.defer="oftalmica.tonometria_oi">
                                </div>
                            </div>
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom border-top">
                                <h6 class="mb-0 text-uppercase">{{ __('Fondo de ojo') }}</h6>
                            </div>
                            <div class="card-body row g-3 py-2 px-2">
                                <div class="col-6">
                                    <label class="form-label">{{ __('O.D.') }}</label>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="oftalmica.fondo_ojo_od">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('O.I.') }}</label>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="oftalmica.fondo_ojo_oi">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0 text-uppercase">{{ __('Anamnesis') }}</h6>
                            </div>
                            <div class="card-body row g-3 py-2 px-2">
                                <div class="col-12">
                                    <textarea class="form-control" rows="6" style="resize: none;" wire:model.defer="oftalmica.anamnesis"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-8">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0 text-uppercase">{{ __('Antecedentes') }}</h6>
                            </div>
                            <div class="card-body row g-3 py-2 px-2">
                                <div class="col-4">
                                    <label class="form-label">{{ __('Personales') }}</label>
                                    <textarea class="form-control mb-3" rows="2" wire:model.defer="oftalmica.antecedentes_personales"></textarea>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">{{ __('Familiares') }}</label>
                                    <textarea class="form-control mb-3" rows="2" wire:model.defer="oftalmica.antecedentes_familiares"></textarea>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">{{ __('Quirúrgicos') }}</label>
                                    <textarea class="form-control" rows="2" wire:model.defer="oftalmica.antecedentes_quirurgicos"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0 text-uppercase">{{ __('Biomicroscopia') }}</h6>
                            </div>
                            <div class="card-body row g-3 py-2 px-2">
                                <div class="col-6">
                                    <label class="form-label">{{ __('O.D.') }}</label>
                                    <textarea class="form-control mb-3" rows="2" wire:model.defer="oftalmica.biomicroscopia_od"></textarea>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('O.I.') }}</label>
                                    <textarea class="form-control" rows="2" wire:model.defer="oftalmica.biomicroscopia_oi"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach (['od' => 'Ojo derecho', 'oi' => 'Ojo izquierdo'] as $lado => $titulo)
                        <div class="col-md-3">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                    <h6 class="mb-0 text-uppercase">{{ __('Diagnostico ') . __($titulo) }}</h6>
                                </div>
                                <div class="card-body">
                                    <label class="form-label">{{ __('Enfermedad') }}</label>
                                    <input type="text" class="form-control mb-3"
                                        wire:model.defer="oftalmica.diagnostico_{{ $lado }}">
                                    <label class="form-label">{{ __('Observacion') }}</label>
                                    <textarea class="form-control" rows="3"
                                        wire:model.defer="oftalmica.diagnostico_{{ $lado }}_observacion"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                    <h6 class="mb-0 text-uppercase">{{ __('Tratamiento ') . __($titulo) }}</h6>
                                </div>
                                <div class="card-body">
                                    <label class="form-label">{{ __('Tratamiento') }}</label>
                                    <input type="text" class="form-control mb-3"
                                        wire:model.defer="oftalmica.tratamiento_{{ $lado }}">
                                    <label class="form-label">{{ __('Observacion') }}</label>
                                    <textarea class="form-control" rows="3"
                                        wire:model.defer="oftalmica.tratamiento_{{ $lado }}_observacion"></textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i>{{ __('Guardar oftalmologia') }}
                    </button>
                </div>
            </form>
        @elseif($tabActiva === 'graduacion' && $tieneTablaGraduacion)
            <form wire:submit="guardarGraduacion">
                @foreach ($bloquesGraduacion as $prefijo => $titulo)
                    <div class="card border shadow-none mb-4">
                        <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                            <h6 class="mb-0">{{ __($titulo) }}</h6>
                        </div>
                        <div class="card-body row g-3 py-2 px-2">
                            <div class="p-2">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center py-1"></th>
                                            @foreach ($camposVision as $campo => $meta)
                                                <th class="text-center py-1">{{ __($meta['label']) }}</th>
                                            @endforeach
                                            <th class="text-center py-1">DIP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ojos as $ojo => $label)
                                            <tr>
                                                <td>
                                                    <strong>{{ __($label) }}</strong>
                                                </td>
                                                @foreach ($camposVision as $campo => $meta)
                                                    <td>
                                                        <input type="{{ $meta['type'] }}"
                                                            class="form-control form-control-sm"
                                                            @if (isset($meta['step'])) step="{{ $meta['step'] }}" @endif
                                                            wire:model.defer="graduacion.{{ $prefijo }}_{{ $ojo }}_{{ $campo }}">
                                                    </td>
                                                @endforeach

                                                @if ($loop->first)
                                                    <td rowspan="2">
                                                        <textarea class="form-control form-control-sm" rows="3" wire:model.defer="graduacion.{{ $prefijo }}_dip"></textarea>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0 text-uppercase">{{ __('Adicion de cerca') }}</h6>
                            </div>
                            <div class="card-body py-2 px-2">
                                <label class="form-label">{{ __('O.D.') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm mb-1"
                                    wire:model.defer="graduacion.adicion_cerca_od">
                                <label class="form-label">{{ __('O.I.') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm"
                                    wire:model.defer="graduacion.adicion_cerca_oi">
                            </div>
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom border-top">
                                <h6 class="mb-0 text-uppercase">{{ __('Adicion intermedia') }}</h6>
                            </div>
                            <div class="card-body py-2 px-2">
                                <label class="form-label">{{ __('O.D.') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm mb-1"
                                    wire:model.defer="graduacion.adicion_intermedia_od">
                                <label class="form-label">{{ __('O.I.') }}</label>
                                <input type="number" step="0.01" class="form-control form-control-sm"
                                    wire:model.defer="graduacion.adicion_intermedia_oi">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0 text-uppercase">{{ __('Atencion') }}</h6>
                            </div>
                            <div class="card-body py-2 px-2">
                                <label class="form-label">{{ __('Fecha de cita') }}</label>
                                <input type="date" class="form-control form-control-sm mb-1"
                                    wire:model.defer="graduacion.fecha_cita">
                                <label class="form-label">{{ __('Fecha de proxima cita') }}</label>
                                <input type="date" class="form-control form-control-sm mb-1"
                                    wire:model.defer="graduacion.fecha_proxima_cita">
                                <label class="form-label">{{ __('Optometra') }}</label>
                                <input type="text" class="form-control form-control-sm mb-1"
                                    wire:model.defer="graduacion.autorefractometro_optometra">
                                <label class="form-label">{{ __('Nro ticket') }}</label>
                                <input type="text" class="form-control form-control-sm mb-3"
                                    wire:model.defer="graduacion.autorefractometro_ticket_numero">
                                <label class="form-label">{{ __('Distancia pupilar') }}</label>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="graduacion.autorefractometro_distancia_pupilar">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card border shadow-none h-100 mb-4">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0">{{ __('Datos de autorefractometro') }}</h6>
                            </div>
                            <div class="card-body row g-4 py-2 px-2">
                                @foreach (['autorefractometroOd' => 'Ojo derecho', 'autorefractometroOi' => 'Ojo izquierdo'] as $campoRaiz => $titulo)
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <h6 class="mb-3">{{ __($titulo) }}</h6>
                                            <div class="row g-2">
                                                <div class="col-4"><label class="form-label">{{ __('Esferico') }}</label></div>
                                                <div class="col-4"><label class="form-label">{{ __('Cilindro') }}</label></div>
                                                <div class="col-4"><label class="form-label">{{ __('Eje') }}</label></div>
                                            </div>
                                            @for ($i = 0; $i < 4; $i++)
                                                <div class="row g-2 mb-2">
                                                    <div class="col-4"><input type="text" class="form-control form-control-sm"
                                                            placeholder="{{ __('Esferico') }}"
                                                            wire:model.defer="{{ $campoRaiz }}.lecturas.{{ $i }}.esferico">
                                                    </div>
                                                    <div class="col-4"><input type="text" class="form-control form-control-sm"
                                                            placeholder="{{ __('Cilindro') }}"
                                                            wire:model.defer="{{ $campoRaiz }}.lecturas.{{ $i }}.cilindro">
                                                    </div>
                                                    <div class="col-4"><input type="text" class="form-control form-control-sm"
                                                            placeholder="{{ __('Eje') }}"
                                                            wire:model.defer="{{ $campoRaiz }}.lecturas.{{ $i }}.eje">
                                                    </div>
                                                </div>
                                            @endfor
                                            <label
                                                class="form-label mt-2">{{ __('Equivalente esferico') }}</label><input
                                                type="text" class="form-control form-control-sm"
                                                wire:model.defer="{{ $campoRaiz }}.equivalente_esferico">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <label class="form-label">{{ __('Recomendaciones') }}</label>
                <textarea class="form-control mb-4" rows="5" wire:model.defer="graduacion.recomendaciones"></textarea>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i>{{ __('Guardar graduacion') }}
                    </button>
                </div>
            </form>
        @elseif($tabActiva === 'contactologia' && $tieneTablaContactologia)
            <form wire:submit="guardarContactologia">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0">{{ __('Queratometria') }}</h6>
                            </div>
                            <div class="card-body py-2 px-2">
                                @foreach ($ojos as $ojo => $label)
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="mb-3">{{ __($label) }}</h6>
                                        <div class="row g-3">
                                            <div class="col-5"><label
                                                    class="form-label">{{ __('Horizontal') }}</label><input
                                                    type="text" class="form-control form-control-sm"
                                                    wire:model.defer="contactologia.queratometria_{{ $ojo }}_horizontal">
                                            </div>
                                            <div class="col-5"><label
                                                    class="form-label">{{ __('Vertical') }}</label><input
                                                    type="text" class="form-control form-control-sm"
                                                    wire:model.defer="contactologia.queratometria_{{ $ojo }}_vertical">
                                            </div>
                                            <div class="col-2"><label
                                                    class="form-label">{{ __('Eje') }}</label><input
                                                    type="number" class="form-control form-control-sm"
                                                    wire:model.defer="contactologia.queratometria_{{ $ojo }}_eje">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                <h6 class="mb-0">{{ __('Especificacion del lente') }}</h6>
                            </div>
                            <div class="card-body row g-3 py-2 px-2">
                                <div class="col-4">
                                    <label class="form-label">{{ __('Material') }}</label>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="contactologia.material">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">{{ __('Tipo de uso') }}</label>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="contactologia.tipo_uso">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">{{ __('Marca') }}</label>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="contactologia.marca">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('Shirmer O.D.') }}</label>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="contactologia.shirmer_od">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('Shirmer O.I.') }}</label>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="contactologia.shirmer_oi">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('BUT O.D.') }}</label>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="contactologia.but_od">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ __('BUT O.I.') }}</label>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="contactologia.but_oi">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach ($contactologiaSecciones as $prefijo => $seccion)
                        <div class="col-md-6">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                    <h6 class="mb-0">{{ __($seccion['titulo']) }}</h6>
                                </div>
                                <div class="card-body py-2 px-2">
                                    @foreach ($ojos as $ojo => $label)
                                        <div class="border rounded p-3 mb-3">
                                            <h6 class="mb-3">{{ __($label) }}</h6>
                                            <div class="row g-3">
                                                @foreach ($seccion['campos'] as $campo)
                                                    <div
                                                        class="col-md-{{ count($seccion['campos']) === 4 ? '3' : (in_array($campo, ['eje'], true) ? '4' : '6') }}">
                                                        <label class="form-label">{{ __(ucfirst($campo)) }}</label>
                                                        <input type="{{ $metaCamposContacto[$campo]['type'] }}"
                                                            @if (isset($metaCamposContacto[$campo]['step'])) step="{{ $metaCamposContacto[$campo]['step'] }}" @endif
                                                            class="form-control form-control-sm"
                                                            wire:model.defer="contactologia.{{ $prefijo }}_{{ $ojo }}_{{ $campo }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i>{{ __('Guardar contactologia') }}
                    </button>
                </div>
            </form>
        @elseif($tabActiva === 'lentes_en_uso' && $tieneTablaLentesEnUso)
            <form wire:submit="guardarLentesEnUso">
                <div class="row g-4 mb-4">

                    @foreach ($ojos as $ojo => $label)
                        <div class="col-md-6">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-label-info py-1 px-2 text-center border-bottom">
                                    <h6 class="mb-0">{{ __('Lentes en uso') }} {{ __($label) }}</h6>
                                </div>
                                <div class="card-body row g-3 py-2 px-2">
                                    <div class="col-6"><label class="form-label">{{ __('Esferico') }}</label><input
                                            type="number" step="0.01" class="form-control form-control-sm"
                                            wire:model.defer="lentesEnUso.{{ $ojo }}_esferico"></div>
                                    <div class="col-6"><label class="form-label">{{ __('Cilindro') }}</label><input
                                            type="number" step="0.01" class="form-control form-control-sm"
                                            wire:model.defer="lentesEnUso.{{ $ojo }}_cilindro"></div>
                                    <div class="col-4"><label class="form-label">{{ __('Eje') }}</label><input
                                            type="number" class="form-control form-control-sm"
                                            wire:model.defer="lentesEnUso.{{ $ojo }}_eje"></div>
                                    <div class="col-4"><label class="form-label">{{ __('A.V C/C') }}</label><input
                                            type="text" class="form-control form-control-sm"
                                            wire:model.defer="lentesEnUso.{{ $ojo }}_av_cc"></div>
                                    <div class="col-4"><label class="form-label">{{ __('Altura') }}</label><input
                                            type="text" class="form-control form-control-sm"
                                            wire:model.defer="lentesEnUso.{{ $ojo }}_altura"></div>
                                    <div class="col-6"><label class="form-label">{{ __('Adicion') }}</label><input
                                            type="number" step="0.01" class="form-control form-control-sm"
                                            wire:model.defer="lentesEnUso.{{ $ojo }}_adicion"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row g-4">
                    <div class="col-md-6"><label class="form-label">{{ __('DIP') }}</label>
                        <textarea class="form-control" rows="4" wire:model.defer="lentesEnUso.dip"></textarea>
                    </div>
                    <div class="col-md-6"><label class="form-label">{{ __('Observaciones') }}</label>
                        <textarea class="form-control" rows="4" wire:model.defer="lentesEnUso.observaciones"></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox"
                                id="lentes-uso-lejos" wire:model.defer="lentesEnUso.usa_lejos"><label
                                class="form-check-label" for="lentes-uso-lejos">{{ __('Lejos') }}</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox"
                                id="lentes-uso-cerca" wire:model.defer="lentesEnUso.usa_cerca"><label
                                class="form-check-label" for="lentes-uso-cerca">{{ __('Cerca') }}</label></div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4"><button type="submit" class="btn btn-primary"><i
                            class="ti tabler-device-floppy me-1"></i>{{ __('Guardar lentes en uso') }}</button></div>
            </form>
        @endif
    </div>
</div>
