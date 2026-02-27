        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('catalogoModal');
            const form = document.getElementById('catalogoModalForm');
            const modalTitle = document.getElementById('catalogoModalLabel');
            const inputCategoriaId = document.getElementById('catalogoCategoriaId');
            const inputTable = document.getElementById('catalogoTable');
            const inputCodigo = document.getElementById('catalogoCodigo');
            const inputDescripcion = document.getElementById('catalogoDescripcion');
            const selectSubcategoria = document.getElementById('catalogoSubcategoria');
            const bloqueCaracteristicas = document.getElementById('bloqueCaracteristicas');
            const catalogoOptions = {};
            const catalogoCategoryMeta = {};

            if (!modalElement || !form || !bloqueCaracteristicas || !selectSubcategoria || !inputCodigo || !
                inputDescripcion) {
                return;
            }

            const camposPorCategoria = {
                1: ['material', 'marca', 'tipo', 'talla', 'color', 'detallecolor', 'clase', 'genero',
                    'presentacion', 'imagen'],
                2: ['material', 'tipo', 'marca', 'fotocromatico', 'tratamiento', 'indice', 'ojobifocal',
                    'adicion', 'diametro', 'imagen'],
                3: ['base', 'material', 'tipo', 'marca', 'fotocromatico', 'tratamiento', 'indice', 'ojobifocal',
                    'adicion', 'diametro', 'medida', 'imagen'],
                4: ['material', 'tipo', 'marca', 'modalidad', 'color', 'detallecolor', 'cb', 'o', 'clase',
                    'presentacion', 'imagen'],
                5: ['material', 'marca', 'tipo', 'talla', 'color', 'colorluna', 'clase', 'genero', 'modelo',
                    'presentacion', 'imagen'],
                6: ['material', 'modelo', 'marca', 'color', 'detallecolor', 'clase', 'genero', 'presentacion',
                    'imagen'],
                7: ['tipo', 'marca', 'clase', 'presentacion', 'imagen'],
                8: ['modelo', 'marca', 'tipo', 'presentacion', 'imagen'],
                9: ['tipo', 'modelo', 'marca', 'presentacion', 'imagen'],
                10: ['tipo', 'modelo']
            };

            const ordenDescripcionPorCategoria = {
                1: ['categoria', 'subcategoria', 'material', 'marca', 'codigo', 'tipo', 'talla', 'color',
                    'detallecolor'
                ],
                2: ['subcategoria', 'material', 'tipo', 'fotocromatico', 'tratamiento', 'diametro'],
                3: ['material', 'tipo', 'fotocromatico', 'tratamiento', 'diametro', 'medida', 'adicion'],
                4: ['categoria', 'subcategoria', 'material', 'tipo', 'marca', 'modalidad', 'color'],
                5: ['categoria', 'subcategoria', 'material', 'marca', 'codigo', 'tipo', 'talla', 'color',
                    'colorluna'
                ],
                6: ['categoria', 'subcategoria', 'material', 'modelo', 'marca', 'color'],
                7: ['categoria', 'subcategoria', 'marca'],
                8: ['subcategoria', 'modelo', 'tipo', 'marca'],
                9: ['subcategoria', 'tipo', 'modelo', 'marca'],
                10: ['subcategoria', 'tipo', 'modelo']
            };

            const labelsCampos = {
                subcategoria: 'Subcategoria',
                material: 'Material',
                marca: 'Marca',
                tipo: 'Tipo',
                talla: 'Talla',
                color: 'Color',
                detallecolor: 'Detalle de color',
                clase: 'Clase',
                genero: 'Genero',
                presentacion: 'Presentacion',
                imagen: 'Imagen',
                fotocromatico: 'Fotocromatico',
                tratamiento: 'Tratamiento',
                indice: 'Indice',
                ojobifocal: 'Ojo bifocal',
                adicion: 'Adicion',
                modalidad: 'Modalidad',
                cb: 'CB',
                o: 'O',
                colorluna: 'Color de luna',
                modelo: 'Modelo',
                base: 'Base',
                diametro: 'Diametro',
                medida: 'Medida'
            };

            let categoriaIdActual = 0;
            let categoriaSlotActual = 0;

            function getOptionsByCategoria(categoriaId) {
                return catalogoOptions[String(categoriaId)] || {};
            }

            function getCategoriaMeta(categoriaId) {
                return catalogoCategoryMeta[String(categoriaId)] || {};
            }

            function escapeHtml(value) {
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderSelect(campo, idCampo, label, options) {
                const opcionesHtml = (options || []).map(function(item) {
                    const codigo = item.codigo || '';
                    return '<option value="' + escapeHtml(item.id) + '" data-codigo="' + escapeHtml(codigo) +
                        '">' + escapeHtml(item.nombre) +
                        '</option>';
                }).join('');

                return '<label class="form-label" for="' + idCampo + '">' + label + '</label>' +
                    '<select class="form-select" id="' + idCampo + '" name="' + campo + '">' +
                    '<option value="">Seleccione</option>' + opcionesHtml + '</select>';
            }

            function renderSubcategoria(categoriaId) {
                const options = getOptionsByCategoria(categoriaId).subcategoria || [];
                const opcionesHtml = options.map(function(item) {
                    const codigo = item.codigo || '';
                    return '<option value="' + escapeHtml(item.id) + '" data-codigo="' + escapeHtml(codigo) +
                        '">' + escapeHtml(item.nombre) +
                        '</option>';
                }).join('');

                selectSubcategoria.innerHTML = '<option value="">Seleccione</option>' + opcionesHtml;
            }

            function renderCaracteristicas(categoriaSlot, categoriaId) {
                const campos = camposPorCategoria[categoriaSlot] || [];
                const optionsCategoria = getOptionsByCategoria(categoriaId);
                bloqueCaracteristicas.innerHTML = '';

                campos.forEach(function(campo) {
                    const label = labelsCampos[campo] || campo;
                    const col = document.createElement('div');
                    const idCampo = 'caracteristica_' + campo;

                    col.className = 'col-md-3';
                    col.innerHTML = renderSelect(campo, idCampo, label, optionsCategoria[campo] || []);

                    bloqueCaracteristicas.appendChild(col);
                });
            }

            function getFieldElement(field) {
                if (field === 'subcategoria') {
                    return selectSubcategoria;
                }

                return document.getElementById('caracteristica_' + field);
            }

            function getDescripcionToken(field) {
                if (field === 'codigo') {
                    return (inputCodigo.value || '').trim();
                }

                if (field === 'categoria') {
                    const categoria = getCategoriaMeta(categoriaIdActual);

                    return (categoria.codigo || categoria.nombre || '').trim();
                }

                const fieldElement = getFieldElement(field);
                if (!fieldElement) {
                    return '';
                }

                if (fieldElement.tagName === 'SELECT') {
                    const selectedOption = fieldElement.options[fieldElement.selectedIndex];
                    if (!selectedOption || !selectedOption.value) {
                        return '';
                    }

                    const codigo = (selectedOption.dataset.codigo || '').trim();
                    const nombre = (selectedOption.textContent || '').trim();

                    return codigo || nombre;
                }

                return (fieldElement.value || '').trim();
            }

            function autocompletarDescripcion() {
                const orden = ordenDescripcionPorCategoria[categoriaSlotActual] || [];
                const partes = [];

                orden.forEach(function(campo) {
                    const token = getDescripcionToken(campo);
                    if (token) {
                        partes.push(token);
                    }
                });

                inputDescripcion.value = partes.join(' ');
            }

            modalElement.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                const dataset = trigger && trigger.dataset ? trigger.dataset : {};
                const categoriaId = Number(dataset.categoriaId || 0);
                const categoriaSlot = Number(dataset.categoriaSlot || 0);
                const categoriaNombre = dataset.categoriaNombre || '';
                const categoriaTabla = dataset.categoriaTabla || 'erp_catalogos';

                inputCategoriaId.value = categoriaId || '';
                inputTable.value = categoriaTabla;
                modalTitle.textContent = categoriaNombre ? ('CATEGOR?A ' + categoriaNombre) : 'CATEGOR?A';
                categoriaIdActual = categoriaId;
                categoriaSlotActual = categoriaSlot;

                renderSubcategoria(categoriaId);
                renderCaracteristicas(categoriaSlot, categoriaId);
                autocompletarDescripcion();
            });

            modalElement.addEventListener('hidden.bs.modal', function() {
                form.reset();
                bloqueCaracteristicas.innerHTML = '';
                inputCategoriaId.value = '';
                inputTable.value = '';
                selectSubcategoria.innerHTML = '<option value="">Seleccione</option>';
                categoriaIdActual = 0;
                categoriaSlotActual = 0;
            });

            form.addEventListener('change', function(event) {
                if (
                    event.target.matches('#catalogoSubcategoria') ||
                    event.target.matches('#bloqueCaracteristicas select')
                ) {
                    autocompletarDescripcion();
                }
            });

            inputCodigo.addEventListener('input', function() {
                autocompletarDescripcion();
            });

            form.addEventListener('submit', function(event) {
                event.preventDefault();
            });
        });
