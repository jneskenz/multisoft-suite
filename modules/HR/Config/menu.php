<?php

return [
    'module' => [
        'name' => 'hr',
        'display_name' => ['es' => 'RRHH', 'en' => 'HR'],
        'icon' => 'ti tabler-users',
        'color' => 'success',
    ],

    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'hr.index',
            'permission' => 'access.hr',
        ],

        // GESTIÓN DE PERSONAL (3 niveles)
        [
            'key' => 'personal',
            'title' => ['es' => 'Gestión de Personal', 'en' => 'Personnel Management'],
            'icon' => 'ti tabler-users-group',
            'permission' => 'hr.employees.view',
            'children' => [
                [
                    'key' => 'empleados',
                    'title' => ['es' => 'Empleados', 'en' => 'Employees'],
                    'icon' => 'ti tabler-user',
                    'route' => 'hr.empleados.index',
                    'permission' => 'hr.employees.view',
                    // 'children' => [
                    //     [
                    //         'key' => 'empleados_list',
                    //         'title' => ['es' => 'Listado', 'en' => 'List'],
                    //         'icon' => 'ti tabler-list',
                    //         'route' => 'hr.empleados.index',
                    //         'permission' => 'hr.employees.view',
                    //     ],
                    //     [
                    //         'key' => 'empleados_import',
                    //         'title' => ['es' => 'Altas / Importación', 'en' => 'Onboarding / Import'],
                    //         'icon' => 'ti tabler-upload',
                    //         'route' => 'hr.empleados.import.index',
                    //         'permission' => 'hr.employees.create',
                    //     ],
                    // ],
                ],

                [
                    'key' => 'contratos',
                    'title' => ['es' => 'Contratos', 'en' => 'Contracts'],
                    'icon' => 'ti tabler-file-text',
                    'route' => 'hr.contratos.index',
                    'permission' => 'hr.contratos.view',
                    // 'children' => [
                    //     [
                    //         'key' => 'contratos_list',
                    //         'title' => ['es' => 'Listado', 'en' => 'List'],
                    //         'icon' => 'ti tabler-list',
                    //         'route' => 'hr.contratos.index',
                    //         'permission' => 'hr.contracts.view',
                    //     ],
                    //     [
                    //         'key' => 'contratos_por_firmar',
                    //         'title' => ['es' => 'Por firmar', 'en' => 'To Sign'],
                    //         'icon' => 'ti tabler-signature',
                    //         'route' => 'hr.contratos.por-firmar.index',
                    //         'permission' => 'hr.contracts.sign',
                    //     ],
                    //     [
                    //         'key' => 'contratos_por_vencer',
                    //         'title' => ['es' => 'Por vencer', 'en' => 'Expiring'],
                    //         'icon' => 'ti tabler-alert-triangle',
                    //         'route' => 'hr.contratos.por-vencer.index',
                    //         'permission' => 'hr.contracts.view',
                    //     ],
                    //     [
                    //         'key' => 'contratos_adendas',
                    //         'title' => ['es' => 'Adendas / Renovaciones', 'en' => 'Addenda / Renewals'],
                    //         'icon' => 'ti tabler-file-plus',
                    //         'route' => 'hr.contratos.adendas.index',
                    //         'permission' => 'hr.contracts.edit',
                    //     ],
                    // ],
                ],

                [
                    'key' => 'ceses_grp',
                    'title' => ['es' => 'Ceses', 'en' => 'Terminations'],
                    'icon' => 'ti tabler-user-x',
                    'permission' => 'hr.terminations.view',
                    'children' => [
                        [
                            'key' => 'ceses_list',
                            'title' => ['es' => 'Registro', 'en' => 'Register'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.ceses.index',
                            'permission' => 'hr.terminations.view',
                        ],
                        [
                            'key' => 'ceses_historial',
                            'title' => ['es' => 'Historial', 'en' => 'History'],
                            'icon' => 'ti tabler-history',
                            'route' => 'hr.ceses.historial.index',
                            'permission' => 'hr.terminations.view',
                        ],
                    ],
                ],

                [
                    'key' => 'expedientes_grp',
                    'title' => ['es' => 'Expedientes', 'en' => 'Employee Files'],
                    'icon' => 'ti tabler-folder',
                    'permission' => 'hr.files.view',
                    'children' => [
                        [
                            'key' => 'expedientes_docs',
                            'title' => ['es' => 'Documentos', 'en' => 'Documents'],
                            'icon' => 'ti tabler-file',
                            'route' => 'hr.expedientes.index',
                            'permission' => 'hr.files.view',
                        ],
                        [
                            'key' => 'expedientes_tipos',
                            'title' => ['es' => 'Categorías / Tipos', 'en' => 'Categories / Types'],
                            'icon' => 'ti tabler-tag',
                            'route' => 'hr.configuracion.tipos-documento.index',
                            'permission' => 'hr.settings.document-types.view',
                        ],
                    ],
                ],
            ],
        ],

        // CONTROL DE ASISTENCIA (3 niveles)
        [
            'key' => 'asistencia',
            'title' => ['es' => 'Control de Asistencia', 'en' => 'Attendance Management'],
            'icon' => 'ti tabler-clock',
            'permission' => 'hr.attendance.view',
            'children' => [
                [
                    'key' => 'marcaciones_grp',
                    'title' => ['es' => 'Marcaciones', 'en' => 'Clock In/Out'],
                    'icon' => 'ti tabler-clock-check',
                    'permission' => 'hr.attendance.view',
                    'children' => [
                        [
                            'key' => 'marcaciones_list',
                            'title' => ['es' => 'Registro', 'en' => 'Records'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.asistencia.marcaciones.index',
                            'permission' => 'hr.attendance.view',
                        ],
                        [
                            'key' => 'marcaciones_ajustes',
                            'title' => ['es' => 'Ajustes / Incidencias', 'en' => 'Adjustments'],
                            'icon' => 'ti tabler-edit',
                            'route' => 'hr.asistencia.marcaciones.ajustes.index',
                            'permission' => 'hr.attendance.edit',
                        ],
                    ],
                ],
                [
                    'key' => 'horarios_grp',
                    'title' => ['es' => 'Horarios', 'en' => 'Schedules'],
                    'icon' => 'ti tabler-calendar-time',
                    'permission' => 'hr.schedules.view',
                    'children' => [
                        [
                            'key' => 'horarios_base',
                            'title' => ['es' => 'Horarios base', 'en' => 'Base Schedules'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.asistencia.horarios.index',
                            'permission' => 'hr.schedules.view',
                        ],
                        [
                            'key' => 'calendarios',
                            'title' => ['es' => 'Calendarios', 'en' => 'Calendars'],
                            'icon' => 'ti tabler-calendar',
                            'route' => 'hr.asistencia.calendarios.index',
                            'permission' => 'hr.schedules.view',
                        ],
                    ],
                ],
                [
                    'key' => 'turnos_grp',
                    'title' => ['es' => 'Turnos', 'en' => 'Shifts'],
                    'icon' => 'ti tabler-calendar-event',
                    'permission' => 'hr.shifts.view',
                    'children' => [
                        [
                            'key' => 'turnos_list',
                            'title' => ['es' => 'Turnos', 'en' => 'Shifts'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.asistencia.turnos.index',
                            'permission' => 'hr.shifts.view',
                        ],
                        [
                            'key' => 'turnos_rotaciones',
                            'title' => ['es' => 'Rotaciones', 'en' => 'Rotations'],
                            'icon' => 'ti tabler-repeat',
                            'route' => 'hr.asistencia.turnos.rotaciones.index',
                            'permission' => 'hr.shifts.edit',
                        ],
                    ],
                ],
                [
                    'key' => 'permisos_grp',
                    'title' => ['es' => 'Permisos', 'en' => 'Leaves'],
                    'icon' => 'ti tabler-calendar-off',
                    'permission' => 'hr.leaves.view',
                    'children' => [
                        [
                            'key' => 'permisos_solicitudes',
                            'title' => ['es' => 'Solicitudes', 'en' => 'Requests'],
                            'icon' => 'ti tabler-file-plus',
                            'route' => 'hr.asistencia.permisos.index',
                            'permission' => 'hr.leaves.view',
                        ],
                        [
                            'key' => 'permisos_aprobaciones',
                            'title' => ['es' => 'Aprobaciones', 'en' => 'Approvals'],
                            'icon' => 'ti tabler-check',
                            'route' => 'hr.asistencia.permisos.aprobaciones.index',
                            'permission' => 'hr.leaves.approve',
                        ],
                    ],
                ],
            ],
        ],

        // VACACIONES (3 niveles)
        [
            'key' => 'vacaciones',
            'title' => ['es' => 'Vacaciones', 'en' => 'Vacations'],
            'icon' => 'ti tabler-beach',
            'permission' => 'hr.vacations.view',
            'children' => [
                [
                    'key' => 'vac_solicitudes_grp',
                    'title' => ['es' => 'Solicitudes', 'en' => 'Requests'],
                    'icon' => 'ti tabler-file-plus',
                    'permission' => 'hr.vacations.view',
                    'children' => [
                        [
                            'key' => 'vac_solicitudes_list',
                            'title' => ['es' => 'Solicitudes', 'en' => 'Requests'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.vacaciones.solicitudes.index',
                            'permission' => 'hr.vacations.view',
                        ],
                        [
                            'key' => 'vac_aprobaciones',
                            'title' => ['es' => 'Aprobaciones', 'en' => 'Approvals'],
                            'icon' => 'ti tabler-check',
                            'route' => 'hr.vacaciones.aprobaciones.index',
                            'permission' => 'hr.vacations.approve',
                        ],
                    ],
                ],
                [
                    'key' => 'vac_calendario_grp',
                    'title' => ['es' => 'Calendario', 'en' => 'Calendar'],
                    'icon' => 'ti tabler-calendar',
                    'permission' => 'hr.vacations.view',
                    'children' => [
                        [
                            'key' => 'vac_calendario',
                            'title' => ['es' => 'Vista calendario', 'en' => 'Calendar View'],
                            'icon' => 'ti tabler-calendar',
                            'route' => 'hr.vacaciones.calendario.index',
                            'permission' => 'hr.vacations.view',
                        ],
                        [
                            'key' => 'vac_ausencias',
                            'title' => ['es' => 'Ausencias', 'en' => 'Absences'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.vacaciones.ausencias.index',
                            'permission' => 'hr.vacations.view',
                        ],
                    ],
                ],
                [
                    'key' => 'vac_saldos_grp',
                    'title' => ['es' => 'Saldos', 'en' => 'Balances'],
                    'icon' => 'ti tabler-calculator',
                    'permission' => 'hr.vacations.view',
                    'children' => [
                        [
                            'key' => 'vac_saldos',
                            'title' => ['es' => 'Saldos', 'en' => 'Balances'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.vacaciones.saldos.index',
                            'permission' => 'hr.vacations.view',
                        ],
                        [
                            'key' => 'vac_politicas',
                            'title' => ['es' => 'Políticas / Reglas', 'en' => 'Policies'],
                            'icon' => 'ti tabler-settings',
                            'route' => 'hr.configuracion.politicas-vacaciones.index',
                            'permission' => 'hr.settings.vacations.view',
                        ],
                    ],
                ],
            ],
        ],

        // PLANILLA (3 niveles)
        [
            'key' => 'planilla',
            'title' => ['es' => 'Planilla', 'en' => 'Payroll'],
            'icon' => 'ti tabler-cash',
            'permission' => 'hr.payroll.view',
            'children' => [
                [
                    'key' => 'pay_proceso_grp',
                    'title' => ['es' => 'Procesamiento', 'en' => 'Processing'],
                    'icon' => 'ti tabler-calculator',
                    'permission' => 'hr.payroll.process',
                    'children' => [
                        [
                            'key' => 'pay_procesar',
                            'title' => ['es' => 'Procesar Planilla', 'en' => 'Process Payroll'],
                            'icon' => 'ti tabler-calculator',
                            'route' => 'hr.planilla.procesar.index',
                            'permission' => 'hr.payroll.process',
                        ],
                        [
                            'key' => 'pay_cts',
                            'title' => ['es' => 'CTS', 'en' => 'CTS'],
                            'icon' => 'ti tabler-piggy-bank',
                            'route' => 'hr.planilla.cts.index',
                            'permission' => 'hr.payroll.cts.view',
                        ],
                        [
                            'key' => 'pay_grati',
                            'title' => ['es' => 'Gratificaciones', 'en' => 'Bonuses'],
                            'icon' => 'ti tabler-gift',
                            'route' => 'hr.planilla.gratificaciones.index',
                            'permission' => 'hr.payroll.bonuses.view',
                        ],
                    ],
                ],
                [
                    'key' => 'pay_catalogos_grp',
                    'title' => ['es' => 'Catálogos', 'en' => 'Catalogs'],
                    'icon' => 'ti tabler-list',
                    'permission' => 'hr.payroll.concepts.view',
                    'children' => [
                        [
                            'key' => 'pay_conceptos',
                            'title' => ['es' => 'Conceptos', 'en' => 'Concepts'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.planilla.conceptos.index',
                            'permission' => 'hr.payroll.concepts.view',
                        ],
                        [
                            'key' => 'pay_estructuras',
                            'title' => ['es' => 'Estructuras / Paquetes', 'en' => 'Salary Structures'],
                            'icon' => 'ti tabler-layout',
                            'route' => 'hr.planilla.estructuras.index',
                            'permission' => 'hr.payroll.structures.view',
                        ],
                    ],
                ],
                [
                    'key' => 'pay_resultados_grp',
                    'title' => ['es' => 'Resultados', 'en' => 'Results'],
                    'icon' => 'ti tabler-file-invoice',
                    'permission' => 'hr.payroll.view',
                    'children' => [
                        [
                            'key' => 'pay_boletas',
                            'title' => ['es' => 'Boletas de Pago', 'en' => 'Pay Slips'],
                            'icon' => 'ti tabler-file-invoice',
                            'route' => 'hr.planilla.boletas.index',
                            'permission' => 'hr.payroll.view',
                        ],
                        [
                            'key' => 'pay_historico',
                            'title' => ['es' => 'Histórico / Cierres', 'en' => 'History / Closings'],
                            'icon' => 'ti tabler-history',
                            'route' => 'hr.planilla.historico.index',
                            'permission' => 'hr.payroll.view',
                        ],
                    ],
                ],
            ],
        ],

        // EVALUACIONES (3 niveles)
        [
            'key' => 'evaluaciones',
            'title' => ['es' => 'Evaluaciones', 'en' => 'Evaluations'],
            'icon' => 'ti tabler-clipboard-check',
            'permission' => 'hr.evaluations.view',
            'children' => [
                [
                    'key' => 'eval_ciclos_grp',
                    'title' => ['es' => 'Ciclos', 'en' => 'Cycles'],
                    'icon' => 'ti tabler-refresh',
                    'permission' => 'hr.evaluations.view',
                    'children' => [
                        [
                            'key' => 'eval_desempeno',
                            'title' => ['es' => 'Desempeño', 'en' => 'Performance'],
                            'icon' => 'ti tabler-chart-line',
                            'route' => 'hr.evaluaciones.desempeno.index',
                            'permission' => 'hr.evaluations.view',
                        ],
                        [
                            'key' => 'eval_seguimiento',
                            'title' => ['es' => 'Seguimiento', 'en' => 'Tracking'],
                            'icon' => 'ti tabler-eye',
                            'route' => 'hr.evaluaciones.seguimiento.index',
                            'permission' => 'hr.evaluations.view',
                        ],
                    ],
                ],
                [
                    'key' => 'eval_catalogos_grp',
                    'title' => ['es' => 'Catálogos', 'en' => 'Catalogs'],
                    'icon' => 'ti tabler-list',
                    'permission' => 'hr.evaluations.view',
                    'children' => [
                        [
                            'key' => 'eval_competencias',
                            'title' => ['es' => 'Competencias', 'en' => 'Competencies'],
                            'icon' => 'ti tabler-star',
                            'route' => 'hr.evaluaciones.competencias.index',
                            'permission' => 'hr.evaluations.competencies.view',
                        ],
                        [
                            'key' => 'eval_objetivos',
                            'title' => ['es' => 'Objetivos', 'en' => 'Goals'],
                            'icon' => 'ti tabler-target',
                            'route' => 'hr.evaluaciones.objetivos.index',
                            'permission' => 'hr.evaluations.goals.view',
                        ],
                    ],
                ],
                [
                    'key' => 'eval_resultados_grp',
                    'title' => ['es' => 'Resultados', 'en' => 'Results'],
                    'icon' => 'ti tabler-report',
                    'permission' => 'hr.evaluations.view',
                    'children' => [
                        [
                            'key' => 'eval_reportes',
                            'title' => ['es' => 'Reportes', 'en' => 'Reports'],
                            'icon' => 'ti tabler-report',
                            'route' => 'hr.evaluaciones.reportes.index',
                            'permission' => 'hr.evaluations.view',
                        ],
                    ],
                ],
            ],
        ],

        // REPORTES (3 niveles)
        [
            'key' => 'reportes',
            'title' => ['es' => 'Reportes', 'en' => 'Reports'],
            'icon' => 'ti tabler-report',
            'permission' => 'hr.reports.view',
            'children' => [
                [
                    'key' => 'rep_personal_grp',
                    'title' => ['es' => 'Personal', 'en' => 'Personnel'],
                    'icon' => 'ti tabler-users',
                    'permission' => 'hr.reports.view',
                    'children' => [
                        [
                            'key' => 'rep_empleados',
                            'title' => ['es' => 'Empleados', 'en' => 'Employees'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.reportes.personal.empleados.index',
                            'permission' => 'hr.reports.view',
                        ],
                        [
                            'key' => 'rep_contratos',
                            'title' => ['es' => 'Contratos', 'en' => 'Contracts'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.reportes.personal.contratos.index',
                            'permission' => 'hr.reports.view',
                        ],
                        [
                            'key' => 'rep_ceses',
                            'title' => ['es' => 'Ceses', 'en' => 'Terminations'],
                            'icon' => 'ti tabler-list',
                            'route' => 'hr.reportes.personal.ceses.index',
                            'permission' => 'hr.reports.view',
                        ],
                    ],
                ],
                [
                    'key' => 'rep_asistencia_grp',
                    'title' => ['es' => 'Asistencia', 'en' => 'Attendance'],
                    'icon' => 'ti tabler-clock',
                    'permission' => 'hr.reports.view',
                    'children' => [
                        [
                            'key' => 'rep_asistencia_resumen',
                            'title' => ['es' => 'Resumen', 'en' => 'Summary'],
                            'icon' => 'ti tabler-chart-bar',
                            'route' => 'hr.reportes.asistencia.resumen.index',
                            'permission' => 'hr.reports.view',
                        ],
                        [
                            'key' => 'rep_asistencia_horas_extra',
                            'title' => ['es' => 'Horas extra / Ausentismo', 'en' => 'Overtime / Absenteeism'],
                            'icon' => 'ti tabler-chart-bar',
                            'route' => 'hr.reportes.asistencia.horas-extra.index',
                            'permission' => 'hr.reports.view',
                        ],
                    ],
                ],
                [
                    'key' => 'rep_planilla_grp',
                    'title' => ['es' => 'Planilla', 'en' => 'Payroll'],
                    'icon' => 'ti tabler-cash',
                    'permission' => 'hr.reports.view',
                    'children' => [
                        [
                            'key' => 'rep_planilla_resumen',
                            'title' => ['es' => 'Resumen', 'en' => 'Summary'],
                            'icon' => 'ti tabler-chart-bar',
                            'route' => 'hr.reportes.planilla.resumen.index',
                            'permission' => 'hr.reports.view',
                        ],
                        [
                            'key' => 'rep_planilla_costos',
                            'title' => ['es' => 'Costos por Centro de Costo', 'en' => 'Costs by Cost Center'],
                            'icon' => 'ti tabler-chart-bar',
                            'route' => 'hr.reportes.planilla.costos.index',
                            'permission' => 'hr.reports.view',
                        ],
                    ],
                ],
                [
                    'key' => 'rep_vacaciones_grp',
                    'title' => ['es' => 'Vacaciones', 'en' => 'Vacations'],
                    'icon' => 'ti tabler-beach',
                    'permission' => 'hr.reports.view',
                    'children' => [
                        [
                            'key' => 'rep_vac_saldos',
                            'title' => ['es' => 'Saldos', 'en' => 'Balances'],
                            'icon' => 'ti tabler-chart-bar',
                            'route' => 'hr.reportes.vacaciones.saldos.index',
                            'permission' => 'hr.reports.view',
                        ],
                        [
                            'key' => 'rep_vac_uso',
                            'title' => ['es' => 'Uso / Calendario', 'en' => 'Usage / Calendar'],
                            'icon' => 'ti tabler-chart-bar',
                            'route' => 'hr.reportes.vacaciones.uso.index',
                            'permission' => 'hr.reports.view',
                        ],
                    ],
                ],
            ],
        ],

        // CAPACITACIONES
        [
            'key' => 'capacitaciones',
            'title' => ['es' => 'Capacitaciones', 'en' => 'Trainings'],
            'icon' => 'ti tabler-school',
            'permission' => 'hr.settings.view',
            'children' => [
                [
                    'key' => 'cap_cursos',
                    'title' => ['es' => 'Cursos y Programas', 'en' => 'Courses & Programs'],
                    'icon' => 'ti tabler-school',
                    'route' => 'hr.capacitaciones.cursos.index',
                    'permission' => 'hr.settings.view',
                ],
                [
                    'key' => 'cap_instructores',
                    'title' => ['es' => 'Instructores', 'en' => 'Instructors'],
                    'icon' => 'ti tabler-user',
                    'route' => 'hr.capacitaciones.instructores.index',
                    'permission' => 'hr.settings.view',
                ],
                [
                    'key' => 'cap_asistencias',
                    'title' => ['es' => 'Asistencias', 'en' => 'Attendance'],
                    'icon' => 'ti tabler-check',
                    'route' => 'hr.capacitaciones.asistencias.index',
                    'permission' => 'hr.settings.view',
                ]
            ]
        ],

        // CONFIGURACIÓN (3 niveles)
        [
            'key' => 'configuracion',
            'title' => ['es' => 'Config. Administrativo', 'en' => 'Admin. Settings'],
            'icon' => 'ti tabler-settings',
            'permission' => 'hr.settings.view',
            'children' => [
                [
                    'key' => 'cfg_org',
                    'title' => ['es' => 'Documentos', 'en' => ''],
                    'icon' => 'ti tabler-books',
                    'permission' => 'hr.settings.view',
                    'children' => [
                        [
                            'key' => 'plantillas_documentos',
                            'title' => ['es' => 'Plantillas', 'en' => 'Templates'],
                            'icon' => 'ti tabler-file-plus',
                            'route' => 'hr.plantillas.index',
                            'permission' => 'hr.templates.view',
                        ],
                    ]
                ],
                [
                    'key' => 'cfg_org',
                    'title' => ['es' => 'Organización', 'en' => 'Organization'],
                    'icon' => 'ti tabler-building',
                    'permission' => 'hr.settings.view',
                    'children' => [
                        [
                            'key' => 'cargos',
                            'title' => ['es' => 'Cargos', 'en' => 'Positions'],
                            'icon' => 'ti tabler-briefcase',
                            'route' => 'hr.cargos.index',
                            'permission' => 'hr.positions.view',
                        ],
                        [
                            'key' => 'departamentos',
                            'title' => ['es' => 'Departamentos', 'en' => 'Departments'],
                            'icon' => 'ti tabler-building',
                            'route' => 'hr.departamentos.index',
                            'permission' => 'hr.departments.view',
                        ],
                        [
                            'key' => 'sedes',
                            'title' => ['es' => 'Sedes / Locales', 'en' => 'Locations'],
                            'icon' => 'ti tabler-building-store',
                            'route' => 'hr.configuracion.sedes.index',
                            'permission' => 'hr.settings.locations.view',
                        ],
                        [
                            'key' => 'centros_costo',
                            'title' => ['es' => 'Centros de Costo / Proyectos', 'en' => 'Cost Centers / Projects'],
                            'icon' => 'ti tabler-clipboard-data',
                            'route' => 'finance.centros-costo.index',
                            'permission' => 'finance.cost-centers.view',
                        ],
                    ],
                ],

                [
                    'key' => 'cfg_contratos',
                    'title' => ['es' => 'Contratos', 'en' => 'Contracts'],
                    'icon' => 'ti tabler-file-text',
                    'permission' => 'hr.settings.contracts.view',
                    'children' => [
                        [
                            'key' => 'tipos-contrato',
                            'title' => ['es' => 'Tipos de Contrato', 'en' => 'Contract Types'],
                            'icon' => 'ti tabler-file-text',
                            'route' => 'hr.configuracion.tipos-contrato.index',
                            'permission' => 'hr.settings.contract-types.view',
                        ],
                        [
                            'key' => 'motivos-contrato',
                            'title' => ['es' => 'Motivos de Contrato', 'en' => 'Contract Reasons'],
                            'icon' => 'ti tabler-list-details',
                            'route' => 'hr.configuracion.motivos-contrato.index',
                            'permission' => 'hr.settings.contract-reasons.view',
                        ],

                    ],
                ],

                [
                    'key' => 'cfg_personal',
                    'title' => ['es' => 'Personal', 'en' => 'People'],
                    'icon' => 'ti tabler-id',
                    'permission' => 'hr.settings.view',
                    'children' => [
                        [
                            'key' => 'tipos-documento',
                            'title' => ['es' => 'Tipos de Documento', 'en' => 'Document Types'],
                            'icon' => 'ti tabler-id',
                            'route' => 'hr.configuracion.tipos-documento.index',
                            'permission' => 'hr.settings.document-types.view',
                        ],
                        [
                            'key' => 'bancos',
                            'title' => ['es' => 'Bancos y Tipos de Cuenta', 'en' => 'Banks & Account Types'],
                            'icon' => 'ti tabler-credit-card',
                            'route' => 'finance.bancos.index',
                            'permission' => 'finance.banks.view',
                        ],
                    ],
                ],

                [
                    'key' => 'cfg_tiempo',
                    'title' => ['es' => 'Tiempo y asistencia', 'en' => 'Time & Attendance'],
                    'icon' => 'ti tabler-clock',
                    'permission' => 'hr.settings.view',
                    'children' => [
                        [
                            'key' => 'tipos-jornada',
                            'title' => ['es' => 'Tipos de Jornada', 'en' => 'Work Schedule Types'],
                            'icon' => 'ti tabler-clock-hour-4',
                            'route' => 'hr.configuracion.tipos-jornada.index',
                            'permission' => 'hr.settings.work-schedule-types.view',
                        ],
                        [
                            'key' => 'tipos-permiso',
                            'title' => ['es' => 'Tipos de Permiso', 'en' => 'Leave Types'],
                            'icon' => 'ti tabler-calendar-off',
                            'route' => 'hr.configuracion.tipos-permiso.index',
                            'permission' => 'hr.settings.leave-types.view',
                        ],
                    ],
                ],

                [
                    'key' => 'cfg_remuneracion',
                    'title' => ['es' => 'Remuneración', 'en' => 'Compensation'],
                    'icon' => 'ti tabler-cash',
                    'permission' => 'hr.settings.view',
                    'children' => [
                        [
                            'key' => 'tipos-beneficio',
                            'title' => ['es' => 'Tipos de Beneficio', 'en' => 'Benefit Types'],
                            'icon' => 'ti tabler-shield-check',
                            'route' => 'hr.configuracion.tipos-beneficio.index',
                            'permission' => 'hr.settings.benefits.view',
                        ],
                        [
                            'key' => 'monedas',
                            'title' => ['es' => 'Monedas', 'en' => 'Currencies'],
                            'icon' => 'ti tabler-coin',
                            'route' => 'admin.monedas.index',
                            'permission' => 'admin.currencies.view',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
