<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
            AdminUserSeeder::class,    // Crear usuario admin con tenant
            SettingsSeeder::class,     // Crear configuraciones por defecto
        ]);
    }
}
