<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('erp_receta_contactologia')) {
            return;
        }

        Schema::create('erp_receta_contactologia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receta_id')->constrained('erp_recetas')->cascadeOnDelete();

            // Queratometria
            $table->string('queratometria_od_horizontal', 30)->nullable();
            $table->string('queratometria_od_vertical', 30)->nullable();
            $table->unsignedSmallInteger('queratometria_od_eje')->nullable();
            $table->string('queratometria_oi_horizontal', 30)->nullable();
            $table->string('queratometria_oi_vertical', 30)->nullable();
            $table->unsignedSmallInteger('queratometria_oi_eje')->nullable();

            // Poder de lente de contacto de prueba
            $table->decimal('prueba_od_esferico', 8, 2)->nullable();
            $table->decimal('prueba_od_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('prueba_od_eje')->nullable();
            $table->decimal('prueba_od_cb', 8, 2)->nullable();
            $table->decimal('prueba_od_diametro', 8, 2)->nullable();

            $table->decimal('prueba_oi_esferico', 8, 2)->nullable();
            $table->decimal('prueba_oi_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('prueba_oi_eje')->nullable();
            $table->decimal('prueba_oi_cb', 8, 2)->nullable();
            $table->decimal('prueba_oi_diametro', 8, 2)->nullable();

            // Lente definitivo
            $table->decimal('definitivo_od_esferico', 8, 2)->nullable();
            $table->decimal('definitivo_od_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('definitivo_od_eje')->nullable();
            $table->decimal('definitivo_od_cb', 8, 2)->nullable();
            $table->decimal('definitivo_od_diametro', 8, 2)->nullable();

            $table->decimal('definitivo_oi_esferico', 8, 2)->nullable();
            $table->decimal('definitivo_oi_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('definitivo_oi_eje')->nullable();
            $table->decimal('definitivo_oi_cb', 8, 2)->nullable();
            $table->decimal('definitivo_oi_diametro', 8, 2)->nullable();

            // Sobre-refraccion
            $table->decimal('sobrerefraccion_od_esferico', 8, 2)->nullable();
            $table->decimal('sobrerefraccion_od_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('sobrerefraccion_od_eje')->nullable();
            $table->string('sobrerefraccion_od_giro', 20)->nullable();

            $table->decimal('sobrerefraccion_oi_esferico', 8, 2)->nullable();
            $table->decimal('sobrerefraccion_oi_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('sobrerefraccion_oi_eje')->nullable();
            $table->string('sobrerefraccion_oi_giro', 20)->nullable();

            // Especificacion del lente
            $table->string('material', 120)->nullable();
            $table->string('tipo_uso', 120)->nullable();
            $table->string('marca', 120)->nullable();

            // Pruebas complementarias
            $table->string('shirmer_od', 30)->nullable();
            $table->string('shirmer_oi', 30)->nullable();
            $table->string('but_od', 30)->nullable();
            $table->string('but_oi', 30)->nullable();
            $table->enum('estado', [0, 1])->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['receta_id'], 'erp_receta_contactologia_receta_unique');
            $table->index(['estado', 'deleted_at'], 'erp_receta_contactologia_estado_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_receta_contactologia');
    }
};