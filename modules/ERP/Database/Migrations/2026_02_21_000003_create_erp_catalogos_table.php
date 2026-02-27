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
        if (Schema::hasTable('erp_catalogos')) {
            return;
        }

        Schema::create('erp_catalogos', function (Blueprint $table) {
            $table->id();

            // Campos base del modal
            $table->foreignId('categoria_id')->nullable()->constrained('erp_categorias')->nullOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('erp_subcategorias')->nullOnDelete();
            $table->string('codigo', 50)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(1);

            // Campos dinamicos del modal (union de todas las categorias)
            $table->foreignId('material_id')->nullable()->constrained('erp_material')->nullOnDelete();
            $table->foreignId('marca_id')->nullable()->constrained('erp_marcas')->nullOnDelete();
            $table->foreignId('tipo_id')->nullable()->constrained('erp_tipos')->nullOnDelete();
            $table->foreignId('talla_id')->nullable()->constrained('erp_tallas')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('erp_colores')->nullOnDelete();
            $table->foreignId('detallecolor_id')->nullable()->constrained('erp_detalle_colores')->nullOnDelete();
            $table->foreignId('clase_id')->nullable()->constrained('erp_clases')->nullOnDelete();
            $table->foreignId('genero_id')->nullable()->constrained('erp_generos')->nullOnDelete();
            $table->foreignId('presentacion_id')->nullable()->constrained('erp_unidad_medidas')->nullOnDelete();
            $table->foreignId('fotocromatico_id')->nullable()->constrained('erp_fotocromaticos')->nullOnDelete();
            $table->foreignId('tratamiento_id')->nullable()->constrained('erp_tratamientos')->nullOnDelete();
            $table->foreignId('indice_id')->nullable()->constrained('erp_indices')->nullOnDelete();
            $table->foreignId('ojobifocal_id')->nullable()->constrained('erp_ojobifocales')->nullOnDelete();
            $table->foreignId('adicion_id')->nullable()->constrained('erp_adiciones')->nullOnDelete();
            $table->foreignId('modalidad_id')->nullable()->constrained('erp_modalidades')->nullOnDelete();
            $table->foreignId('cb_id')->nullable()->constrained('erp_cb')->nullOnDelete();
            $table->foreignId('o_id')->nullable()->constrained('erp_o')->nullOnDelete();
            $table->foreignId('colorluna_id')->nullable()->constrained('erp_colores')->nullOnDelete();
            $table->foreignId('modelo_id')->nullable()->constrained('erp_modelos')->nullOnDelete();
            $table->foreignId('base_id')->nullable()->constrained('erp_poderes')->nullOnDelete();
            $table->foreignId('medida_id')->nullable()->constrained('erp_unidad_medidas')->nullOnDelete();
            $table->foreignId('diametro_id')->nullable()->constrained('erp_diametros')->nullOnDelete();
            $table->string('imagen', 255)->nullable();

            // id del usar para created_by y updated_by
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('codigo');
            $table->index(['categoria_id', 'subcategoria_id']);
            $table->index(['categoria_id', 'estado', 'deleted_at'], 'erp_catalogos_cat_estado_deleted_idx');
            $table->index(['categoria_id', 'created_at'], 'erp_catalogos_cat_created_idx');
            $table->unique(['categoria_id', 'codigo'], 'erp_catalogos_cat_codigo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_catalogos');
    }
};
