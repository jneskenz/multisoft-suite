<?php

namespace Modules\ERP\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TodoCatalogosSeeder::class,
            CombinacionesSeeder::class,
            TicketsSeeder::class,            
        ]);
    }
}
