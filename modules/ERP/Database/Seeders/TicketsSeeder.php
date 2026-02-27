<?php

namespace Modules\ERP\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\GroupCompany;

class TicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (
            !Schema::hasTable('erp_tickets')
            || !Schema::hasTable('core_group_companies')
            || !Schema::hasTable('partners_personas')
            || !Schema::hasTable('partners_tipo_personas')
        ) {
            $this->command?->warn('Tablas requeridas para TicketsSeeder no encontradas.');

            return;
        }

        $groups = GroupCompany::query()
            ->select(['id', 'tenant_id', 'code'])
            ->whereIn('code', ['PE', 'EC'])
            ->get();

        if ($groups->isEmpty()) {
            $this->command?->warn('No hay grupos base (PE/EC). Ejecuta seeders de Core primero.');

            return;
        }

        $userId = Schema::hasTable('users')
            ? (User::query()->where('email', 'admin@multisoft.test')->value('id') ?? User::query()->value('id'))
            : null;

        foreach ($groups as $group) {
            $patients = DB::table('partners_personas as p')
                ->join('partners_tipo_personas as tp', 'tp.persona_id', '=', 'p.id')
                ->where('p.tenant_id', (int) $group->tenant_id)
                ->where(function ($query) use ($group): void {
                    $query->where('p.group_company_id', (int) $group->id)
                        ->orWhereNull('p.group_company_id');
                })
                ->where('p.estado', true)
                ->whereNull('p.deleted_at')
                ->where('tp.tipo', 'paciente')
                ->where('tp.estado', true)
                ->select('p.id')
                ->distinct()
                ->limit(4)
                ->get();

            if ($patients->isEmpty()) {
                continue;
            }

            $estados = ['abierto', 'en_proceso', 'listo', 'cerrado'];
            $prioridades = ['normal', 'alta', 'baja', 'normal'];
            $canales = ['mostrador', 'cita', 'telefono', 'web'];

            foreach ($patients as $index => $patient) {
                $seq = $index + 1;
                $ticketNumero = sprintf('TK-%s-%06d', $group->code, $seq);

                $fechaTicket = now()->subDays(4 - $seq)->setTime(9 + $seq, 15);
                $estadoTicket = $estados[$index % count($estados)];
                $subtotal = (float) (120 + ($seq * 25));
                $descuento = (float) (($index % 2 === 0) ? 10 : 0);
                $impuesto = round(($subtotal - $descuento) * 0.18, 2);
                $total = round($subtotal - $descuento + $impuesto, 2);
                $saldo = in_array($estadoTicket, ['cerrado', 'listo'], true) ? 0 : round($total * 0.4, 2);

                DB::table('erp_tickets')->updateOrInsert(
                    [
                        'tenant_id' => (int) $group->tenant_id,
                        'ticket_numero' => $ticketNumero,
                    ],
                    [
                        'group_company_id' => (int) $group->id,
                        'paciente_id' => (int) $patient->id,
                        'fecha_ticket' => $fechaTicket,
                        'estado_ticket' => $estadoTicket,
                        'prioridad' => $prioridades[$index % count($prioridades)],
                        'canal' => $canales[$index % count($canales)],
                        'resumen' => 'Ticket de atencion demo para pruebas ERP.',
                        'moneda' => 'PEN',
                        'subtotal' => $subtotal,
                        'descuento_total' => $descuento,
                        'impuesto_total' => $impuesto,
                        'total' => $total,
                        'saldo_pendiente' => $saldo,
                        'fecha_cierre' => $estadoTicket === 'cerrado' ? now()->subDays(1)->setTime(18, 0) : null,
                        'cerrado_por' => $estadoTicket === 'cerrado' ? $userId : null,
                        'estado' => true,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'deleted_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command?->info('Tickets de ERP creados/actualizados correctamente.');
    }
}
