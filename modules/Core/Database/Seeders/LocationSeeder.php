<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Company;
use Modules\Core\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::query()->get()->keyBy('code');

        if ($companies->isEmpty()) {
            $this->command?->warn('No hay empresas. Ejecuta CompanySeeder primero.');
            return;
        }

        $locations = [
            [
                'company_code' => 'PE-MAIN',
                'code' => 'PE-LIM-01',
                'name' => 'Sede Lima Central',
                'timezone' => 'America/Lima',
                'address' => 'Av. Principal 123, Lima',
                'city' => 'Lima',
                'phone' => '+51 1 234 5678',
                'status' => 'active',
            ],
            [
                'company_code' => 'PE-MAIN',
                'code' => 'PE-AQP-01',
                'name' => 'Sucursal Arequipa',
                'timezone' => 'America/Lima',
                'address' => 'Av. Ejercito 560, Arequipa',
                'city' => 'Arequipa',
                'phone' => '+51 54 234 567',
                'status' => 'active',
            ],
            [
                'company_code' => 'PE-RETAIL',
                'code' => 'PE-LIM-RET-01',
                'name' => 'Retail Lima Norte',
                'timezone' => 'America/Lima',
                'address' => 'Av. Industrial 890, Lima',
                'city' => 'Lima',
                'phone' => '+51 1 456 7890',
                'status' => 'active',
            ],
            [
                'company_code' => 'EC-MAIN',
                'code' => 'EC-GYE-01',
                'name' => 'Sede Guayaquil',
                'timezone' => 'America/Guayaquil',
                'address' => 'Av. 9 de Octubre 456, Guayaquil',
                'city' => 'Guayaquil',
                'phone' => '+593 4 234 5678',
                'status' => 'active',
            ],
        ];

        foreach ($locations as $item) {
            $company = $companies->get($item['company_code']);

            if (!$company) {
                continue;
            }

            Location::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => $item['code'],
                ],
                [
                    'company_id' => $company->id,
                    'name' => $item['name'],
                    'timezone' => $item['timezone'],
                    'address' => $item['address'],
                    'city' => $item['city'],
                    'phone' => $item['phone'],
                    'is_main' => $item['is_main'] ?? false,
                    'status' => $item['status'],
                ]
            );
        }

        $this->command?->info('Locales core creados/actualizados correctamente.');
    }
}
