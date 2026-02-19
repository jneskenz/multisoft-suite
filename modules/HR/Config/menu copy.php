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
        
        // GESTIÓN DE PERSONAL
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
                ],
                [
                    'key' => 'contratos',
                    'title' => ['es' => 'Contratos', 'en' => 'Contracts'],
                    'icon' => 'ti tabler-file-text',
                    'route' => 'hr.contratos.index',
                    'permission' => 'hr.contracts.view',
                ],
                [
                    'key' => 'ceses',
                    'title' => ['es' => 'Ceses', 'en' => 'Terminations'],
                    'icon' => 'ti tabler-user-x',
                    'route' => 'hr.ceses.index',
                    'permission' => 'hr.terminations.view',
                ],
                [
                    'key' => 'expedientes',
                    'title' => ['es' => 'Expedientes', 'en' => 'Employee Files'],
                    'icon' => 'ti tabler-folder',
                    'route' => 'hr.expedientes.index',
                    'permission' => 'hr.files.view',
                ],
            ],
        ],
        
        // ASISTENCIA Y TIEMPO
        [
            'key' => 'asistencia',
            'title' => ['es' => 'Asistencia y Tiempo', 'en' => 'Attendance & Time'],
            'icon' => 'ti tabler-clock',
            'permission' => 'hr.attendance.view',
            'children' => [
                [
                    'key' => 'marcaciones',
                    'title' => ['es' => 'Marcaciones', 'en' => 'Clock In/Out'],
                    'icon' => 'ti tabler-clock-check',
                    'route' => 'hr.asistencia.marcaciones.index',
                    'permission' => 'hr.attendance.view',
                ],
                [
                    'key' => 'horarios',
                    'title' => ['es' => 'Horarios', 'en' => 'Schedules'],
                    'icon' => 'ti tabler-calendar-time',
                    'route' => 'hr.asistencia.horarios.index',
                    'permission' => 'hr.schedules.view',
                ],
                [
                    'key' => 'turnos',
                    'title' => ['es' => 'Turnos', 'en' => 'Shifts'],
                    'icon' => 'ti tabler-calendar-event',
                    'route' => 'hr.asistencia.turnos.index',
                    'permission' => 'hr.shifts.view',
                ],
                [
                    'key' => 'permisos',
                    'title' => ['es' => 'Permisos', 'en' => 'Leaves'],
                    'icon' => 'ti tabler-calendar-off',
                    'route' => 'hr.asistencia.permisos.index',
                    'permission' => 'hr.leaves.view',
                ],
            ],
        ],
        
        // VACACIONES
        [
            'key' => 'vacaciones',
            'title' => ['es' => 'Vacaciones', 'en' => 'Vacations'],
            'icon' => 'ti tabler-beach',
            'permission' => 'hr.vacations.view',
            'children' => [
                [
                    'key' => 'solicitudes',
                    'title' => ['es' => 'Solicitudes', 'en' => 'Requests'],
                    'icon' => 'ti tabler-file-plus',
                    'route' => 'hr.vacaciones.solicitudes.index',
                    'permission' => 'hr.vacations.view',
                ],
                [
                    'key' => 'calendario',
                    'title' => ['es' => 'Calendario', 'en' => 'Calendar'],
                    'icon' => 'ti tabler-calendar',
                    'route' => 'hr.vacaciones.calendario.index',
                    'permission' => 'hr.vacations.view',
                ],
                [
                    'key' => 'saldos',
                    'title' => ['es' => 'Saldos', 'en' => 'Balances'],
                    'icon' => 'ti tabler-calculator',
                    'route' => 'hr.vacaciones.saldos.index',
                    'permission' => 'hr.vacations.view',
                ],
            ],
        ],
        
        // PLANILLA Y REMUNERACIONES
        [
            'key' => 'planilla',
            'title' => ['es' => 'Planilla', 'en' => 'Payroll'],
            'icon' => 'ti tabler-cash',
            'permission' => 'hr.payroll.view',
            'children' => [
                [
                    'key' => 'procesar',
                    'title' => ['es' => 'Procesar Planilla', 'en' => 'Process Payroll'],
                    'icon' => 'ti tabler-calculator',
                    'route' => 'hr.planilla.procesar.index',
                    'permission' => 'hr.payroll.process',
                ],
                [
                    'key' => 'conceptos',
                    'title' => ['es' => 'Conceptos', 'en' => 'Concepts'],
                    'icon' => 'ti tabler-list',
                    'route' => 'hr.planilla.conceptos.index',
                    'permission' => 'hr.payroll.concepts.view',
                ],
                [
                    'key' => 'boletas',
                    'title' => ['es' => 'Boletas de Pago', 'en' => 'Pay Slips'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'hr.planilla.boletas.index',
                    'permission' => 'hr.payroll.view',
                ],
                [
                    'key' => 'cts',
                    'title' => ['es' => 'CTS', 'en' => 'CTS'],
                    'icon' => 'ti tabler-piggy-bank',
                    'route' => 'hr.planilla.cts.index',
                    'permission' => 'hr.payroll.cts.view',
                ],
                [
                    'key' => 'gratificaciones',
                    'title' => ['es' => 'Gratificaciones', 'en' => 'Bonuses'],
                    'icon' => 'ti tabler-gift',
                    'route' => 'hr.planilla.gratificaciones.index',
                    'permission' => 'hr.payroll.bonuses.view',
                ],
            ],
        ],
        
        // EVALUACIONES Y DESEMPEÑO
        [
            'key' => 'evaluaciones',
            'title' => ['es' => 'Evaluaciones', 'en' => 'Evaluations'],
            'icon' => 'ti tabler-clipboard-check',
            'permission' => 'hr.evaluations.view',
            'children' => [
                [
                    'key' => 'desempeno',
                    'title' => ['es' => 'Desempeño', 'en' => 'Performance'],
                    'icon' => 'ti tabler-chart-line',
                    'route' => 'hr.evaluaciones.desempeno.index',
                    'permission' => 'hr.evaluations.view',
                ],
                [
                    'key' => 'competencias',
                    'title' => ['es' => 'Competencias', 'en' => 'Competencies'],
                    'icon' => 'ti tabler-star',
                    'route' => 'hr.evaluaciones.competencias.index',
                    'permission' => 'hr.evaluations.competencies.view',
                ],
                [
                    'key' => 'objetivos',
                    'title' => ['es' => 'Objetivos', 'en' => 'Goals'],
                    'icon' => 'ti tabler-target',
                    'route' => 'hr.evaluaciones.objetivos.index',
                    'permission' => 'hr.evaluations.goals.view',
                ],
            ],
        ],
        
        // REPORTES
        [
            'key' => 'reportes',
            'title' => ['es' => 'Reportes', 'en' => 'Reports'],
            'icon' => 'ti tabler-report',
            'permission' => 'hr.reports.view',
            'children' => [
                [
                    'key' => 'personal',
                    'title' => ['es' => 'Personal', 'en' => 'Personnel'],
                    'icon' => 'ti tabler-users',
                    'route' => 'hr.reportes.personal.index',
                    'permission' => 'hr.reports.view',
                ],
                [
                    'key' => 'asistencia',
                    'title' => ['es' => 'Asistencia', 'en' => 'Attendance'],
                    'icon' => 'ti tabler-clock',
                    'route' => 'hr.reportes.asistencia.index',
                    'permission' => 'hr.reports.view',
                ],
                [
                    'key' => 'planilla',
                    'title' => ['es' => 'Planilla', 'en' => 'Payroll'],
                    'icon' => 'ti tabler-cash',
                    'route' => 'hr.reportes.planilla.index',
                    'permission' => 'hr.reports.view',
                ],
                [
                    'key' => 'vacaciones',
                    'title' => ['es' => 'Vacaciones', 'en' => 'Vacations'],
                    'icon' => 'ti tabler-beach',
                    'route' => 'hr.reportes.vacaciones.index',
                    'permission' => 'hr.reports.view',
                ],
            ],
        ],
        
        // CONFIGURACIÓN
        [
            'key' => 'configuracion',
            'title' => ['es' => 'Config. Administrativo', 'en' => 'Admin. Settings'],
            'icon' => 'ti tabler-settings',
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
                    'key' => 'tipos-contrato',
                    'title' => ['es' => 'Tipos de Contrato', 'en' => 'Contract Types'],
                    'icon' => 'ti tabler-file-text',
                    'route' => 'hr.configuracion.tipos-contrato.index',
                    'permission' => 'hr.settings.contract-types.view',
                ],
                [
                    'key' => 'plantillas',
                    'title' => ['es' => 'Plantillas', 'en' => 'Template'],
                    'icon' => 'ti tabler-files',
                    'route' => 'hr.configuracion.plantillas.index',
                    'permission' => 'hr.settings.contract-types.view',
                ],
                [
                    'key' => 'motivos-cese',
                    'title' => ['es' => 'Motivos de Cese', 'en' => 'Termination Reasons'],
                    'icon' => 'ti tabler-list',
                    'route' => 'hr.configuracion.motivos-cese.index',
                    'permission' => 'hr.settings.termination-reasons.view',
                ],
            ],
        ],
    ],
];
