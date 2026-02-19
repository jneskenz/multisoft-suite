<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            SettingsSeeder::class,

            CompanySeeder::class,
            LocationSeeder::class,
        ]);
    }
}
