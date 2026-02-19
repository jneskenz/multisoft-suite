<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Company;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\GroupCompany;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tenant por defecto (cliente inicial)
        $tenant = Tenant::updateOrCreate(
            ['code' => 'TNT-001'],
            [
                'slug' => 'multisoft',
                'name' => 'Multisoft Suite',
                'legal_name' => 'Multisoft Solutions SAC',
                'tax_id' => '20123456789',
                'contact_name' => 'Administrador',
                'contact_email' => 'admin@multisoft.pe',
                'contact_phone' => '+51 999 999 999',
                'plan' => 'enterprise',
                'billing_cycle' => 'yearly',
                'max_users' => 100,
                'max_group_companies' => 10,
                'max_companies' => 50,
                'max_locations' => 200,
                'modules_enabled' => ['core', 'erp', 'crm', 'hr', 'fms', 'reports'],
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addYear(),
                'primary_color' => '#7367F0',
                'status' => 'active',
            ]
        );

        $this->command->info('Tenant creado: ' . $tenant->name);

        // Crear grupo empresa para Perú
        $groupPE = GroupCompany::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'country_code' => 'PE',
            ],
            [
                'code' => 'PE',
                'business_name' => 'Multisoft Solutions Perú SAC',
                'trade_name' => 'Multisoft Perú',
                'tax_id' => '20123456789',
                'tax_id_type' => 'RUC',
                'app_name' => 'Multisoft Suite',
                'address' => 'Av. Principal 123, Lima',
                'city' => 'Lima',
                'phone' => '+51 1 234 5678',
                'email' => 'peru@multisoft.pe',
                'website' => 'https://multisoft.pe',
                'timezone' => 'America/Lima',
                'locale' => 'es',
                'currency_code' => 'PEN',
                'currency_symbol' => 'S/',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'status' => 'active',
            ]
        );

        $this->command->info('Grupo empresa creado: ' . $groupPE->business_name . ' (' . $groupPE->code . ')');

        // Crear grupo empresa para Ecuador (ejemplo de expansión)
        $groupEC = GroupCompany::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'country_code' => 'EC',
            ],
            [
                'code' => 'EC',
                'business_name' => 'Multisoft Solutions Ecuador SA',
                'trade_name' => 'Multisoft Ecuador',
                'tax_id' => '0912345678001',
                'tax_id_type' => 'RUC',
                'app_name' => 'Multisoft Suite',
                'address' => 'Av. 9 de Octubre 456, Guayaquil',
                'city' => 'Guayaquil',
                'phone' => '+593 4 234 5678',
                'email' => 'ecuador@multisoft.pe',
                'website' => 'https://multisoft.ec',
                'timezone' => 'America/Guayaquil',
                'locale' => 'es',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'status' => 'active',
            ]
        );

        $this->command->info('Grupo empresa creado: ' . $groupEC->business_name . ' (' . $groupEC->code . ')');

        // Aquí se podrían crear empresas 
        $company = Company::updateOrCreate(
            [
                'group_company_id' => $groupPE->id,
                'code' => 'PE-001',
            ],
            [
                'name' => 'Multisoft Solutions Perú SAC',
                'tax_id' => '20123456789',
                // 'tax_id_type' => 'RUC',
                'address' => 'Av. Principal 123, Lima',
                // 'city' => 'Lima',
                'phone' => '+51 1 234 5678',
                'email' => 'contacto@multisoft.pe',
                // 'website' => 'https://multisoft.pe',
                'status' => 'active',
            ]
        );


    }
}
