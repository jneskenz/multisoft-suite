<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\CompanySeeder;
use Modules\Core\Database\Seeders\LocationSeeder;
use Modules\Core\Database\Seeders\TenantSeeder;
use Modules\Core\Database\Seeders\SettingsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            TenantSeeder::class,       // Crear tenant y grupos empresa
            CompanySeeder::class,      // Crear empresas por grupo
            LocationSeeder::class,     // Crear locales por empresa
            AdminUserSeeder::class,    // Crear usuario admin con tenant
            SettingsSeeder::class,     // Crear configuraciones por defecto
        ]);
    }
}
