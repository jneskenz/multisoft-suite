<?php

return [
    'name' => 'Partners',
    'permissions' => [
        'access.partners',
        'partners.view',
        'partners.create',
        'partners.edit',
        'partners.delete',
        'partners.export',
        'partners.import',
        'partners.personas.view',
        'partners.empresas.view',
        'partners.relaciones.view',
        'partners.clientes.view',
        'partners.proveedores.view',
        'partners.pacientes.view',
        // Compatibilidad con nomenclatura inicial
        'partners.customers.view',
        'partners.suppliers.view',
        'partners.contacts.view',
    ],
];
