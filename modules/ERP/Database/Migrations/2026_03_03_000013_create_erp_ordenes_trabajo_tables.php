<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('erp_ordenes_trabajo')) {
            Schema::create('erp_ordenes_trabajo', function (Blueprint $table) {
                $table->id();

                $table->foreignId('tenant_id')->constrained('core_tenants')->cascadeOnDelete();
                $table->foreignId('group_company_id')->nullable()->constrained('core_group_companies')->nullOnDelete();
                $table->foreignId('ticket_id')->nullable()->constrained('erp_tickets')->nullOnDelete();
                $table->foreignId('receta_id')->nullable()->constrained('erp_recetas')->nullOnDelete();
                $table->foreignId('paciente_id')->nullable()->constrained('partners_personas')->nullOnDelete();

                $table->string('numero_ot', 50);
                $table->dateTime('fecha_orden');
                $table->dateTime('fecha_prometida')->nullable();

                $table->string('tipo_orden', 30)->default('lentes_completos')
                    ->comment('lentes_completos, cambio_lunas, contactologia, servicio_externo, reparacion');
                $table->string('estado_ot', 30)->default('pendiente')
                    ->comment('pendiente, en_laboratorio, control_calidad, listo, entregado, anulado');
                $table->string('prioridad', 20)->default('normal')
                    ->comment('normal, urgente');

                $table->unsignedBigInteger('local_id')->nullable()
                    ->comment('Pendiente de enlazar con tabla ERP de locales');
                $table->unsignedBigInteger('almacen_id')->nullable()
                    ->comment('Pendiente de enlazar con tabla ERP de almacenes');

                $table->text('observaciones')->nullable();
                $table->text('indicaciones_entrega')->nullable();
                $table->enum('estado', [1, 0])->default(1);

                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['tenant_id', 'numero_ot'], 'erp_ordenes_trabajo_tenant_numero_unique');
                $table->index(['tenant_id', 'group_company_id'], 'erp_ot_tenant_group_idx');
                $table->index(['ticket_id', 'estado_ot'], 'erp_ot_ticket_estado_idx');
                $table->index(['receta_id', 'estado_ot'], 'erp_ot_receta_estado_idx');
                $table->index(['paciente_id', 'fecha_orden'], 'erp_ot_paciente_fecha_idx');
                $table->index(['tipo_orden', 'estado_ot'], 'erp_ot_tipo_estado_idx');
                $table->index(['prioridad', 'fecha_prometida'], 'erp_ot_prioridad_prometida_idx');
                $table->index(['estado', 'deleted_at'], 'erp_ot_estado_deleted_idx');
            });
        }

        if (!Schema::hasTable('erp_orden_trabajo_detalles')) {
            Schema::create('erp_orden_trabajo_detalles', function (Blueprint $table) {
                $table->id();

                $table->foreignId('orden_trabajo_id')->constrained('erp_ordenes_trabajo')->cascadeOnDelete();

                $table->unsignedInteger('secuencia')->default(1);
                $table->string('tipo_detalle', 30)->default('producto')
                    ->comment('producto, lente, servicio, accesorio, reparacion');

                $table->foreignId('catalogo_id')->nullable()->constrained('erp_catalogos')->nullOnDelete();
                $table->foreignId('matriz_lente_id')->nullable()->constrained('erp_matriz_lentes')->nullOnDelete();
                $table->unsignedBigInteger('producto_id')->nullable()
                    ->comment('Pendiente de enlazar con tabla ERP de productos');

                $table->string('descripcion', 255);
                $table->decimal('cantidad', 12, 2)->default(1);
                $table->string('unidad', 20)->default('UND');
                $table->decimal('precio_unitario', 12, 2)->nullable();
                $table->decimal('subtotal', 12, 2)->nullable();
                $table->string('estado_detalle', 30)->default('pendiente')
                    ->comment('pendiente, en_proceso, listo, entregado, anulado');
                $table->text('observaciones')->nullable();
                $table->enum('estado', [1, 0])->default(1);

                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['orden_trabajo_id', 'secuencia'], 'erp_ot_detalle_ot_secuencia_unique');
                $table->index(['orden_trabajo_id', 'estado_detalle'], 'erp_ot_detalle_ot_estado_idx');
                $table->index(['tipo_detalle', 'catalogo_id'], 'erp_ot_detalle_tipo_catalogo_idx');
                $table->index(['matriz_lente_id', 'estado'], 'erp_ot_detalle_matriz_estado_idx');
                $table->index(['producto_id', 'estado'], 'erp_ot_detalle_producto_estado_idx');
                $table->index(['estado', 'deleted_at'], 'erp_ot_detalle_estado_deleted_idx');
            });
        }

        if (!Schema::hasTable('erp_orden_trabajo_lentes')) {
            Schema::create('erp_orden_trabajo_lentes', function (Blueprint $table) {
                $table->id();

                $table->foreignId('orden_trabajo_detalle_id')->constrained('erp_orden_trabajo_detalles')->cascadeOnDelete();

                $table->string('tipo_vision', 30)->nullable()
                    ->comment('monofocal, bifocal, progresivo, ocupacional, contacto');

                $table->unsignedBigInteger('material_id')->nullable()
                    ->comment('Pendiente de enlazar con catalogo de materiales');
                $table->unsignedBigInteger('tratamiento_id')->nullable()
                    ->comment('Pendiente de enlazar con catalogo de tratamientos');
                $table->unsignedBigInteger('color_id')->nullable()
                    ->comment('Pendiente de enlazar con catalogo de colores');
                $table->unsignedBigInteger('diseno_id')->nullable()
                    ->comment('Pendiente de enlazar con catalogo de disenos');
                $table->unsignedBigInteger('indice_id')->nullable()
                    ->comment('Pendiente de enlazar con catalogo de indices');

                $table->decimal('od_esferico', 8, 2)->nullable();
                $table->decimal('od_cilindro', 8, 2)->nullable();
                $table->unsignedSmallInteger('od_eje')->nullable();
                $table->decimal('od_adicion', 8, 2)->nullable();

                $table->decimal('oi_esferico', 8, 2)->nullable();
                $table->decimal('oi_cilindro', 8, 2)->nullable();
                $table->unsignedSmallInteger('oi_eje')->nullable();
                $table->decimal('oi_adicion', 8, 2)->nullable();

                $table->decimal('dp', 8, 2)->nullable();
                $table->decimal('altura_oblea', 8, 2)->nullable();
                $table->text('observaciones_tecnicas')->nullable();
                $table->enum('estado', [1, 0])->default(1);

                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['orden_trabajo_detalle_id'], 'erp_ot_lente_detalle_unique');
                $table->index(['tipo_vision', 'estado'], 'erp_ot_lente_tipo_estado_idx');
                $table->index(['material_id', 'tratamiento_id'], 'erp_ot_lente_material_trat_idx');
                $table->index(['estado', 'deleted_at'], 'erp_ot_lente_estado_deleted_idx');
            });
        }

        if (!Schema::hasTable('erp_orden_trabajo_historial')) {
            Schema::create('erp_orden_trabajo_historial', function (Blueprint $table) {
                $table->id();

                $table->foreignId('orden_trabajo_id')->constrained('erp_ordenes_trabajo')->cascadeOnDelete();

                $table->string('estado_anterior', 30)->nullable();
                $table->string('estado_nuevo', 30);
                $table->text('observacion')->nullable();
                $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamp('created_at')->useCurrent();

                $table->index(['orden_trabajo_id', 'created_at'], 'erp_ot_historial_ot_fecha_idx');
                $table->index(['estado_nuevo', 'created_at'], 'erp_ot_historial_estado_fecha_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_orden_trabajo_historial');
        Schema::dropIfExists('erp_orden_trabajo_lentes');
        Schema::dropIfExists('erp_orden_trabajo_detalles');
        Schema::dropIfExists('erp_ordenes_trabajo');
    }
};
