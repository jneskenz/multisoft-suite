<?php

namespace Modules\ERP\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CombinacionesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seed('erp_serie_visual', [
                ['codigo' => '1 serie',   'nombre' => '1 Serie'],
                ['codigo' => '1 serie a', 'nombre' => '1 Serie A'],
                ['codigo' => '1 serie b', 'nombre' => '1 Serie B'],
                ['codigo' => '1 serie c', 'nombre' => '1 Serie C'],
                ['codigo' => '2 serie',   'nombre' => '2 Serie'],
                ['codigo' => '2 serie a', 'nombre' => '2 Serie A'],
                ['codigo' => '2 serie b', 'nombre' => '2 Serie B'],
                ['codigo' => '2 serie c', 'nombre' => '2 Serie C'],
                ['codigo' => '3 serie',   'nombre' => '3 Serie'],
                ['codigo' => '3 serie a', 'nombre' => '3 Serie A'],
                ['codigo' => '3 serie b', 'nombre' => '3 Serie B'],
                ['codigo' => '3 serie c', 'nombre' => '3 Serie C'],
                ['codigo' => '4 serie',   'nombre' => '4 Serie'],
            ]);

            $serieId = DB::table('erp_serie_visual')->where('codigo', '1 serie')->value('id');

            $this->seed('erp_subserie_visual', [
                ['codigo' => 'Esf 2.00 Cyl 2.00',       'nombre' => 'Esf 2.00 Cyl 2.00',       'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 4.00 Cy 2.00',  'nombre' => 'Hasta Esf 4.00 Cy 2.00',  'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 6.00 Cyl 2.00', 'nombre' => 'Hasta Esf 6.00 Cyl 2.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 2.00 Cyl 4.00', 'nombre' => 'Hasta Esf 2.00 Cyl 4.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 4.00 Cyl 4.00', 'nombre' => 'Hasta Esf 4.00 Cyl 4.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 6.00 Cyl 4.00', 'nombre' => 'Hasta Esf 6.00 Cyl 4.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 2.00 Cyl 6.00', 'nombre' => 'Hasta Esf 2.00 Cyl 6.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 4.00 Cyl 6.00', 'nombre' => 'Hasta Esf 4.00 Cyl 6.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 6.00 Cyl 6.00', 'nombre' => 'Hasta Esf 6.00 Cyl 6.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 2.00 Cyl 8.00', 'nombre' => 'Hasta Esf 2.00 Cyl 8.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Hasta Esf 8.00 Cyl 2.00', 'nombre' => 'Hasta Esf 8.00 Cyl 2.00', 'serie_visual_id' => $serieId],
                ['codigo' => 'Ninguno',                  'nombre' => 'Ninguno',                  'serie_visual_id' => $serieId],
            ]);

            $this->seed('erp_medida_cilindrica', [
                ['codigo' => '-8.00', 'nombre' => '-8.00'],
                ['codigo' => '-7.75', 'nombre' => '-7.75'],
                ['codigo' => '-7.50', 'nombre' => '-7.50'],
                ['codigo' => '-7.25', 'nombre' => '-7.25'],
                ['codigo' => '-7.00', 'nombre' => '-7.00'],
                ['codigo' => '-6.75', 'nombre' => '-6.75'],
                ['codigo' => '-6.50', 'nombre' => '-6.50'],
                ['codigo' => '-6.25', 'nombre' => '-6.25'],
                ['codigo' => '-6.00', 'nombre' => '-6.00'],
                ['codigo' => '-5.75', 'nombre' => '-5.75'],
                ['codigo' => '-5.50', 'nombre' => '-5.50'],
                ['codigo' => '-5.25', 'nombre' => '-5.25'],
                ['codigo' => '-5.00', 'nombre' => '-5.00'],
                ['codigo' => '-4.75', 'nombre' => '-4.75'],
                ['codigo' => '-4.50', 'nombre' => '-4.50'],
                ['codigo' => '-4.25', 'nombre' => '-4.25'],
                ['codigo' => '-4.00', 'nombre' => '-4.00'],
                ['codigo' => '-3.75', 'nombre' => '-3.75'],
                ['codigo' => '-3.50', 'nombre' => '-3.50'],
                ['codigo' => '-3.25', 'nombre' => '-3.25'],
                ['codigo' => '-3.00', 'nombre' => '-3.00'],
                ['codigo' => '-2.75', 'nombre' => '-2.75'],
                ['codigo' => '-2.50', 'nombre' => '-2.50'],
                ['codigo' => '-2.25', 'nombre' => '-2.25'],
                ['codigo' => '-2.00', 'nombre' => '-2.00'],
                ['codigo' => '-1.75', 'nombre' => '-1.75'],
                ['codigo' => '-1.50', 'nombre' => '-1.50'],
                ['codigo' => '-1.25', 'nombre' => '-1.25'],
                ['codigo' => '-1.00', 'nombre' => '-1.00'],
                ['codigo' => '-0.75', 'nombre' => '-0.75'],
                ['codigo' => '-0.50', 'nombre' => '-0.50'],
                ['codigo' => '-0.25', 'nombre' => '-0.25'],
                ['codigo' => '0.00',  'nombre' => '0.00'],
            ]);

            $this->seed('erp_medida_esferica', [
                ['codigo' => '-10.00', 'nombre' => '-10.00'],
                ['codigo' => '-9.75', 'nombre' => '-9.75'],
                ['codigo' => '-9.50', 'nombre' => '-9.50'],
                ['codigo' => '-9.25', 'nombre' => '-9.25'],
                ['codigo' => '-9.00', 'nombre' => '-9.00'],
                ['codigo' => '-8.75', 'nombre' => '-8.75'],
                ['codigo' => '-8.50', 'nombre' => '-8.50'],
                ['codigo' => '-8.25', 'nombre' => '-8.25'],
                ['codigo' => '-8.00', 'nombre' => '-8.00'],
                ['codigo' => '-7.75', 'nombre' => '-7.75'],
                ['codigo' => '-7.50', 'nombre' => '-7.50'],
                ['codigo' => '-7.25', 'nombre' => '-7.25'],
                ['codigo' => '-7.00', 'nombre' => '-7.00'],
                ['codigo' => '-6.75', 'nombre' => '-6.75'],
                ['codigo' => '-6.50', 'nombre' => '-6.50'],
                ['codigo' => '-6.25', 'nombre' => '-6.25'],
                ['codigo' => '-6.00', 'nombre' => '-6.00'],
                ['codigo' => '-5.75', 'nombre' => '-5.75'],
                ['codigo' => '-5.50', 'nombre' => '-5.50'],
                ['codigo' => '-5.25', 'nombre' => '-5.25'],
                ['codigo' => '-5.00', 'nombre' => '-5.00'],
                ['codigo' => '-4.75', 'nombre' => '-4.75'],
                ['codigo' => '-4.50', 'nombre' => '-4.50'],
                ['codigo' => '-4.25', 'nombre' => '-4.25'],
                ['codigo' => '-4.00', 'nombre' => '-4.00'],
                ['codigo' => '-3.75', 'nombre' => '-3.75'],
                ['codigo' => '-3.50', 'nombre' => '-3.50'],
                ['codigo' => '-3.25', 'nombre' => '-3.25'],
                ['codigo' => '-3.00', 'nombre' => '-3.00'],
                ['codigo' => '-2.75', 'nombre' => '-2.75'],
                ['codigo' => '-2.50', 'nombre' => '-2.50'],
                ['codigo' => '-2.25', 'nombre' => '-2.25'],
                ['codigo' => '-2.00', 'nombre' => '-2.00'],
                ['codigo' => '-1.75', 'nombre' => '-1.75'],
                ['codigo' => '-1.50', 'nombre' => '-1.50'],
                ['codigo' => '-1.25', 'nombre' => '-1.25'],
                ['codigo' => '-1.00', 'nombre' => '-1.00'],
                ['codigo' => '-0.75', 'nombre' => '-0.75'],
                ['codigo' => '-0.50', 'nombre' => '-0.50'],
                ['codigo' => '-0.25', 'nombre' => '-0.25'],
                ['codigo' => '0.00',  'nombre' => '0.00'],
                ['codigo' => '0.25',  'nombre' => '0.25'],
                ['codigo' => '0.50',  'nombre' => '0.50'],
                ['codigo' => '0.75',  'nombre' => '0.75'],
                ['codigo' => '1.00',  'nombre' => '1.00'],
                ['codigo' => '1.25',  'nombre' => '1.25'],
                ['codigo' => '1.50',  'nombre' => '1.50'],
                ['codigo' => '1.75',  'nombre' => '1.75'],
                ['codigo' => '2.00',  'nombre' => '2.00'],
                ['codigo' => '2.25',  'nombre' => '2.25'],
                ['codigo' => '2.50',  'nombre' => '2.50'],
                ['codigo' => '2.75',  'nombre' => '2.75'],
                ['codigo' => '3.00',  'nombre' => '3.00'],
                ['codigo' => '3.25',  'nombre' => '3.25'],
                ['codigo' => '3.50',  'nombre' => '3.50'],
                ['codigo' => '3.75',  'nombre' => '3.75'],
                ['codigo' => '4.00',  'nombre' => '4.00'],
                ['codigo' => '4.25',  'nombre' => '4.25'],
                ['codigo' => '4.50',  'nombre' => '4.50'],
                ['codigo' => '4.75',  'nombre' => '4.75'],
                ['codigo' => '5.00',  'nombre' => '5.00'],
                ['codigo' => '5.25',  'nombre' => '5.25'],
                ['codigo' => '5.50',  'nombre' => '5.50'],
                ['codigo' => '5.75',  'nombre' => '5.75'],
                ['codigo' => '6.00',  'nombre' => '6.00'],
                ['codigo' => '6.25',  'nombre' => '6.25'],
                ['codigo' => '6.50',  'nombre' => '6.50'],
                ['codigo' => '6.75',  'nombre' => '6.75'],
                ['codigo' => '7.00',  'nombre' => '7.00'],
                ['codigo' => '7.25',  'nombre' => '7.25'],
                ['codigo' => '7.50',  'nombre' => '7.50'],
                ['codigo' => '7.75',  'nombre' => '7.75'],
                ['codigo' => '8.00',  'nombre' => '8.00'],
                ['codigo' => '8.25',  'nombre' => '8.25'],
                ['codigo' => '8.50',  'nombre' => '8.50'],
                ['codigo' => '8.75',  'nombre' => '8.75'],
                ['codigo' => '9.00',  'nombre' => '9.00'],
                ['codigo' => '9.25',  'nombre' => '9.25'],
                ['codigo' => '9.50',  'nombre' => '9.50'],
                ['codigo' => '9.75',  'nombre' => '9.75'],
                ['codigo' => '10.00', 'nombre' => '10.00'],
            ]);

            $this->seed('erp_medida_esferica', [
                ['codigo' => '-10.00', 'nombre' => '-10.00'],
                ['codigo' => '-9.75', 'nombre' => '-9.75'],
                ['codigo' => '-9.50', 'nombre' => '-9.50'],
                ['codigo' => '-9.25', 'nombre' => '-9.25'],
                ['codigo' => '-9.00', 'nombre' => '-9.00'],
                ['codigo' => '-8.75', 'nombre' => '-8.75'],
                ['codigo' => '-8.50', 'nombre' => '-8.50'],
            ]);



            $this->seed('erp_adiciones', [
                ['codigo' => 'NINGUNA', 'nombre' => 'NINGUNA', 'categoria_id' => '2'],
                ['codigo' => '+1.00', 'nombre' => '+1.00', 'categoria_id' => '2'],
                ['codigo' => '+1.25', 'nombre' => '+1.25', 'categoria_id' => '2'],
                ['codigo' => '+1.50', 'nombre' => '+1.50', 'categoria_id' => '2'],
                ['codigo' => '+1.75', 'nombre' => '+1.75', 'categoria_id' => '2'],
                ['codigo' => '+2.00', 'nombre' => '+2.00', 'categoria_id' => '2'],
                ['codigo' => '+2.25', 'nombre' => '+2.25', 'categoria_id' => '2'],
                ['codigo' => '+2.50', 'nombre' => '+2.50', 'categoria_id' => '2'],
                ['codigo' => '+2.75', 'nombre' => '+2.75', 'categoria_id' => '2'],
                ['codigo' => '+3.00', 'nombre' => '+3.00', 'categoria_id' => '2'],
            ]);


        });
    }

    private function seed(string $table, array $rows): void
    {
        if (!Schema::hasTable($table)) return;

        $columns = Schema::getColumnListing($table);
        $existing = DB::table($table)->pluck('id', 'codigo')->all();
        $now = now();

        foreach ($rows as $row) {
            $row = array_merge(['estado' => 1], $row);
            $row = array_intersect_key($row, array_flip($columns));

            if (isset($existing[$row['codigo']])) {
                DB::table($table)->where('id', $existing[$row['codigo']])->update($row + ['updated_at' => $now]);
            } else {
                DB::table($table)->insert($row + ['created_at' => $now, 'updated_at' => $now]);
            }
        }
    }
}