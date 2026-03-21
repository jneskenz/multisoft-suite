<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TipoDepartamentoSeeder::class,
            DepartamentoSeeder::class,
            CargoSeeder::class,
            EmpleadoSeeder::class,
            PlantillasDocumentoSeeder::class,
        ]);
    }
}
