<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Keep only one current assignment per employee before creating the unique partial index.
        DB::statement(
            <<<'SQL'
            WITH ranked AS (
                SELECT id,
                       ROW_NUMBER() OVER (
                           PARTITION BY empleado_id
                           ORDER BY fecha_inicio DESC, id DESC
                       ) AS rn
                FROM hr_empleado_cargos
                WHERE es_actual = true
                  AND deleted_at IS NULL
            )
            UPDATE hr_empleado_cargos AS ec
            SET es_actual = false,
                fecha_fin = COALESCE(ec.fecha_fin, CURRENT_DATE),
                updated_at = NOW()
            FROM ranked
            WHERE ec.id = ranked.id
              AND ranked.rn > 1;
            SQL
        );

        DB::statement(
            "CREATE UNIQUE INDEX IF NOT EXISTS uk_hr_empleado_cargos_actual_unico
             ON hr_empleado_cargos (empleado_id)
             WHERE es_actual = true AND deleted_at IS NULL"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS uk_hr_empleado_cargos_actual_unico');
    }
};
