<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Company;
use Modules\Core\Models\GroupCompany;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = GroupCompany::query()->get()->keyBy('code');

        if ($groups->isEmpty()) {
            $this->command?->warn('No hay grupos empresa. Ejecuta TenantSeeder primero.');
            return;
        }

        $companies = [
            [
                'group_code' => 'PE',
                'code' => 'PE-MAIN',
                'name' => 'Inversiones Multilens I.R.L.',
                'trade_name' => 'Ópticas Multilens',
                'tax_id' => '20554911511',
                'timezone' => 'America/Lima',
                'address' => 'Av. Principal 123, Lima',
                'phone' => '+51 1 234 5678',
                'email' => 'peru@multisoft.pe',
                'status' => 'active',
            ],
            [
                'group_code' => 'PE',
                'code' => 'PE-RETAIL',
                'name' => 'Medilens I.R.L.',
                'trade_name' => 'Ópticas Medilens',
                'tax_id' => '20608320050',
                'timezone' => 'America/Lima',
                'address' => 'Jr. Comercio 450, Lima',
                'phone' => '+51 1 345 6789',
                'email' => 'retail.pe@multisoft.pe',
                'status' => 'active',
            ],
            [
                'group_code' => 'EC',
                'code' => 'EC-MAIN',
                'name' => 'Multisoft Solutions Ecuador SA',
                'trade_name' => 'Multisoft Ecuador',
                'tax_id' => '0912345678001',
                'timezone' => 'America/Guayaquil',
                'address' => 'Av. 9 de Octubre 456, Guayaquil',
                'phone' => '+593 4 234 5678',
                'email' => 'ecuador@multisoft.ec',
                'status' => 'active',
            ],
        ];

        foreach ($companies as $item) {
            $group = $groups->get($item['group_code']);

            if (!$group) {
                continue;
            }

            Company::updateOrCreate(
                [
                    'group_company_id' => $group->id,
                    'code' => $item['code'],
                ],
                [
                    'group_company_id' => $group->id,
                    'name' => $item['name'],
                    'trade_name' => $item['trade_name'],
                    'tax_id' => $item['tax_id'],
                    'timezone' => $item['timezone'],
                    'address' => $item['address'],
                    'phone' => $item['phone'],
                    'email' => $item['email'],
                    'status' => $item['status'],
                ]
            );
        }

        $this->command?->info('Empresas core creadas/actualizadas correctamente.');
    }
}
