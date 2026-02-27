<?php

namespace Modules\Partners\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PartnersEmpresasSeeder::class,
            PartnersPersonasSeeder::class,
            PartnersTipoPersonasSeeder::class,
            PartnersPersonaEmpresaSeeder::class,
        ]);
    }
}
