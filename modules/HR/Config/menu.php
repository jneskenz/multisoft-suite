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
