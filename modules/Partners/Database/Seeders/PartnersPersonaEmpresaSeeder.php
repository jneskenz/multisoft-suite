<?php

namespace Modules\Partners\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\GroupCompany;
use Modules\Partners\Models\Empresa;
use Modules\Partners\Models\Persona;
use Modules\Partners\Models\PersonaEmpresa;

class PartnersPersonaEmpresaSeeder extends Seeder
{
    public function run(): void
    {
        if (
            !Schema::hasTable('partners_persona_empresa')
            || !Schema::hasTable('partners_personas')
            || !Schema::hasTable('partners_empresas')
        ) {
            $this->command?->warn('Tablas partners_persona_empresa/partners_personas/partners_empresas no encontradas.');

            return;
        }

        if (!Schema::hasTable('core_group_companies')) {
            $this->command?->warn('Tabla core_group_companies no encontrada. Ejecuta migraciones de Core.');

            return;
        }

        $groups = GroupCompany::query()
            ->select(['id', 'tenant_id', 'code'])
            ->whereIn('code', ['PE', 'EC'])
            ->get()
            ->keyBy('code');

        if ($groups->isEmpty()) {
            $this->command?->warn('No hay grupos base (PE/EC). Ejecuta seeders de Core primero.');

            return;
        }

        $userId = Schema::hasTable('users')
            ? (User::query()->where('email', 'admin@multisoft.test')->value('id') ?? User::query()->value('id'))
            : null;

        $rows = [
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '72345678',
                'ruc' => '20612345671',
                'tipo_relacion' => 'titular',
                'es_principal' => true,
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '72345678',
                'ruc' => '20612345673',
                'tipo_relacion' => 'contacto',
                'es_principal' => false,
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '45678912',
                'ruc' => '20612345671',
                'tipo_relacion' => 'contacto',
                'es_principal' => true,
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '47896521',
                'ruc' => '20612345672',
                'tipo_relacion' => 'representante_legal',
                'es_principal' => true,
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '49876543',
                'ruc' => '20612345671',
                'tipo_relacion' => 'empleado',
                'es_principal' => false,
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '49876543',
                'ruc' => '20612345672',
                'tipo_relacion' => 'apoderado',
                'es_principal' => true,
                'estado' => true,
            ],
            [
                'group_code' => 'EC',
                'tipo_documento' => 'CEDULA',
                'numero_documento' => '0912345678',
                'ruc' => '1791234567001',
                'tipo_relacion' => 'titular',
                'es_principal' => true,
                'estado' => true,
            ],
            [
                'group_code' => 'EC',
                'tipo_documento' => 'CEDULA',
                'numero_documento' => '0923456789',
                'ruc' => '1791234567002',
                'tipo_relacion' => 'representante_legal',
                'es_principal' => true,
                'estado' => true,
            ],
            [
                'group_code' => 'EC',
                'tipo_documento' => 'CEDULA',
                'numero_documento' => '0923456789',
                'ruc' => '1791234567001',
                'tipo_relacion' => 'contacto',
                'es_principal' => false,
                'estado' => true,
            ],
        ];

        foreach ($rows as $row) {
            $group = $groups->get($row['group_code']);
            if (!$group) {
                continue;
            }

            $persona = Persona::query()
                ->where('tenant_id', (int) $group->tenant_id)
                ->where('tipo_documento', $row['tipo_documento'])
                ->where('numero_documento', $row['numero_documento'])
                ->first();

            $empresa = Empresa::query()
                ->where('tenant_id', (int) $group->tenant_id)
                ->where('ruc', $row['ruc'])
                ->first();

            if (!$persona || !$empresa) {
                continue;
            }

            $relacion = PersonaEmpresa::updateOrCreate(
                [
                    'persona_id' => $persona->id,
                    'empresa_id' => $empresa->id,
                ],
                [
                    'tipo_relacion' => $row['tipo_relacion'],
                    'es_principal' => (bool) $row['es_principal'],
                    'estado' => (bool) $row['estado'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            if ((bool) $row['es_principal']) {
                PersonaEmpresa::query()
                    ->where('persona_id', $persona->id)
                    ->where('id', '!=', $relacion->id)
                    ->update([
                        'es_principal' => false,
                        'updated_by' => $userId,
                        'updated_at' => now(),
                    ]);
            }
        }

        $this->command?->info('Partners relaciones persona-empresa creadas/actualizadas correctamente.');
    }
}
