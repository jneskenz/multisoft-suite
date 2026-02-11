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
        [
            'key' => 'administrativo',
            'title' => ['es' => 'Administrativo', 'en' => 'Administrative'],
            'icon' => 'ti tabler-clipboard-list',
            'permission' => 'hr.employees.view',
            'children' => [
                [
                    'key' => 'empleados',
                    'title' => ['es' => 'Empleados', 'en' => 'Employees'],
                    'icon' => 'ti tabler-users',
                    'route' => 'hr.administrativo.empleados.index',
                    'permission' => 'hr.employees.view',
                ],
                [
                    'key' => 'documentos',
                    'title' => ['es' => 'Documentos', 'en' => 'Documents'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'hr.administrativo.documentos.index',
                    'permission' => 'hr.employees.view',
                ],
            ],
        ],
        [
            'key' => 'gestion-personal',
            'title' => ['es' => 'Gestión de personal', 'en' => 'Personnel Management'],
            'icon' => 'ti tabler-clipboard-list',
            'permission' => 'hr.employees.view',
            'children' => [
                [
                    'key' => 'empleados',
                    'title' => ['es' => 'Empleados', 'en' => 'Employees'],
                    'icon' => 'ti tabler-users',
                    'route' => 'hr.empleados.index',
                    'permission' => 'hr.empleados.view',
                ],
                [
                    'key' => 'estructura-organizacional',
                    'title' => ['es' => 'Estructura Organizacional', 'en' => 'Organizational Structure'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'hr.gestion-personal.estructura-organizacional.index',
                    'permission' => 'hr.employees.view',
                ],
                [
                    'key' => 'movimientos-personal',
                    'title' => ['es' => 'Movimientos de Personal', 'en' => 'Personnel Movements'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'hr.gestion-personal.movimientos-personal.index',
                    'permission' => 'hr.employees.view',
                ],
                [
                    'key' => 'contratos-laborales',
                    'title' => ['es' => 'Contratos Laborales', 'en' => 'Labor Contracts'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'hr.gestion-personal.contratos-laborales.index',
                    'permission' => 'hr.employees.view',
                ],
                [
                    'key' => 'expediente-digital',
                    'title' => ['es' => 'Expediente Digital', 'en' => 'Digital File'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'hr.gestion-personal.expediente-digital.index',
                    'permission' => 'hr.employees.view',
                ],
            ],
        ],
        [
            'key' => 'employees',
            'title' => ['es' => 'Empleados', 'en' => 'Employees'],
            'icon' => 'ti tabler-users',
            'route' => 'hr.employees.index',
            'permission' => 'hr.employees.view',
        ],
        [
            'key' => 'attendance',
            'title' => ['es' => 'Asistencia', 'en' => 'Attendance'],
            'icon' => 'ti tabler-clock-check',
            'route' => 'hr.attendance.index',
            'permission' => 'hr.attendance.view',
        ],
        [
            'key' => 'payroll',
            'title' => ['es' => 'Planilla', 'en' => 'Payroll'],
            'icon' => 'ti tabler-cash',
            'route' => 'hr.payroll.index',
            'permission' => 'hr.payroll.view',
        ],
    ],
];
