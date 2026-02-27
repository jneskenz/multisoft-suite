<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\DatabaseSeeder as CoreDatabaseSeeder;
use Modules\ERP\Database\Seeders\DatabaseSeeder as ERPDatabaseSeeder;
use Modules\HR\Database\Seeders\DatabaseSeeder as HRDatabaseSeeder;
use Modules\Partners\Database\Seeders\DatabaseSeeder as PartnersDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            CoreDatabaseSeeder::class, // Crear empresas por grupo
            PartnersDatabaseSeeder::class, // Datos base de Partners
            ERPDatabaseSeeder::class,  // Catalogos base de ERP
            HRDatabaseSeeder::class,   // Catalogos base de HR
        ]);
    }
}
