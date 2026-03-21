<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('erp_matriz_lentes')) {
            Schema::create('erp_matriz_lentes', function (Blueprint $table) {
                $table->id();

                $table->foreignId('catalogo_id')->constrained('erp_catalogos')->cascadeOnDelete();
                $table->foreignId('combinacion_medida_id')->constrained('erp_combinacion_medidas')->cascadeOnDelete();
                $table->foreignId('categoria_id')->nullable()->constrained('erp_categorias')->nullOnDelete();

                // Campos desnormalizados para consultas rapidas y generacion de stock.
                $table->foreignId('serie_visual_id')->constrained('erp_serie_visual')->cascadeOnDelete();
                $table->foreignId('subserie_visual_id')->constrained('erp_subserie_visual')->cascadeOnDelete();
                $table->foreignId('adicion_id')->constrained('erp_adiciones')->cascadeOnDelete();
                $table->foreignId('medida_esferica_id')->constrained('erp_medida_esferica')->cascadeOnDelete();
                $table->foreignId('medida_cilindrica_id')->constrained('erp_medida_cilindrica')->cascadeOnDelete();

                $table->string('codigo_matriz', 120)->nullable();
                $table->timestamp('generado_at')->nullable();
                $table->enum('estado', [1, 0])->default(1);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(
                    [
                        'catalogo_id',
                        'combinacion_medida_id',
                        'adicion_id',
                        'medida_esferica_id',
                        'medida_cilindrica_id',
                    ],
                    'erp_matriz_lentes_unique'
                );

                $table->index(['catalogo_id', 'estado', 'deleted_at'], 'erp_matriz_lentes_cat_estado_idx');
                $table->index(['categoria_id', 'serie_visual_id'], 'erp_matriz_lentes_cat_serie_idx');
                $table->index(['subserie_visual_id', 'adicion_id'], 'erp_matriz_lentes_subserie_adicion_idx');
                $table->index('codigo_matriz', 'erp_matriz_lentes_codigo_idx');
                $table->index('generado_at', 'erp_matriz_lentes_generado_idx');
            });
        }

        if (!Schema::hasTable('erp_matriz_lentes_stock')) {
            Schema::create('erp_matriz_lentes_stock', function (Blueprint $table) {
                $table->id();

                $table->foreignId('matriz_lente_id')->constrained('erp_matriz_lentes')->cascadeOnDelete();
                // Sin FK por ahora: aun no existe tabla de almacenes en el modulo ERP.
                $table->unsignedBigInteger('almacen_id')->comment('Pendiente de enlazar con tabla ERP de almacenes');

                $table->decimal('stock_actual', 12, 2)->default(0);
                $table->decimal('stock_reservado', 12, 2)->default(0);
                $table->decimal('stock_disponible', 12, 2)->default(0);
                $table->decimal('stock_minimo', 12, 2)->default(0);
                $table->decimal('stock_maximo', 12, 2)->nullable();
                $table->decimal('costo_promedio', 12, 4)->default(0);
                $table->decimal('ultimo_costo', 12, 4)->default(0);
                $table->decimal('precio_venta', 12, 2)->nullable();
                $table->enum('estado', [1, 0])->default(1);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['matriz_lente_id', 'almacen_id'], 'erp_matriz_lentes_stock_unique');
                $table->index(['almacen_id', 'estado', 'deleted_at'], 'erp_matriz_lentes_stock_almacen_idx');
                $table->index(['stock_actual', 'stock_disponible'], 'erp_matriz_lentes_stock_saldos_idx');
            });
        }

        if (!Schema::hasTable('erp_matriz_lentes_movimientos')) {
            Schema::create('erp_matriz_lentes_movimientos', function (Blueprint $table) {
                $table->id();

                $table->foreignId('matriz_lente_id')->constrained('erp_matriz_lentes')->cascadeOnDelete();
                // Sin FK por ahora: aun no existe tabla de almacenes en el modulo ERP.
                $table->unsignedBigInteger('almacen_id')->comment('Pendiente de enlazar con tabla ERP de almacenes');

                $table->string('tipo_movimiento', 30);
                $table->decimal('cantidad', 12, 2);
                $table->decimal('stock_anterior', 12, 2)->default(0);
                $table->decimal('stock_nuevo', 12, 2)->default(0);
                $table->string('referencia_tipo', 50)->nullable();
                $table->unsignedBigInteger('referencia_id')->nullable();
                $table->text('observacion')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['matriz_lente_id', 'almacen_id'], 'erp_matriz_mov_mat_alm_idx');
                $table->index(['tipo_movimiento', 'created_at'], 'erp_matriz_mov_tipo_fecha_idx');
                $table->index(['referencia_tipo', 'referencia_id'], 'erp_matriz_mov_ref_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_matriz_lentes_movimientos');
        Schema::dropIfExists('erp_matriz_lentes_stock');
        Schema::dropIfExists('erp_matriz_lentes');
    }
};
