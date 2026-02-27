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
        if (Schema::hasTable('erp_combinacion_medidas')) {
            return;
        }

        Schema::create('erp_combinacion_medidas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('catalogo_id')->constrained('erp_catalogos')->cascadeOnDelete();
            $table->foreignId('serie_visual_id')->constrained('erp_serie_visual')->cascadeOnDelete();
            $table->foreignId('subserie_visual_id')->constrained('erp_subserie_visual')->cascadeOnDelete();
            $table->foreignId('medida_esferica_desde_id')->constrained('erp_medida_esferica')->cascadeOnDelete();
            $table->foreignId('medida_esferica_hasta_id')->constrained('erp_medida_esferica')->cascadeOnDelete();
            $table->foreignId('medida_cilindrica_desde_id')->constrained('erp_medida_cilindrica')->cascadeOnDelete();
            $table->foreignId('medida_cilindrica_hasta_id')->constrained('erp_medida_cilindrica')->cascadeOnDelete();
            $table->foreignId('adicion_desde_id')->constrained('erp_adiciones')->cascadeOnDelete();
            $table->foreignId('adicion_hasta_id')->constrained('erp_adiciones')->cascadeOnDelete();
            
            $table->decimal('preciobase', 10, 2);

            $table->decimal('precio_x_menor_minimo', 10, 2);
            $table->decimal('precio_x_menor_base', 10, 2);
            $table->decimal('precio_x_menor_maximo', 10, 2);
            
            $table->decimal('precio_x_mayor_minimo', 10, 2);
            $table->decimal('precio_x_mayor_base', 10, 2);
            $table->decimal('precio_x_mayor_maximo', 10, 2);

            $table->enum('estado', [1, 0])->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_combinacion_medidas');
    }
};
