<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('erp_receta_graduaciones')) {
            return;
        }

        Schema::create('erp_receta_graduaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receta_id')->constrained('erp_recetas')->cascadeOnDelete();

            // Vision de lejos
            $table->decimal('lejos_od_esferico', 8, 2)->nullable();
            $table->decimal('lejos_od_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('lejos_od_eje')->nullable();
            $table->string('lejos_od_av', 30)->nullable();
            $table->string('lejos_od_prisma', 30)->nullable();
            $table->string('lejos_od_base', 30)->nullable();
            $table->string('lejos_od_dnp', 30)->nullable();

            $table->decimal('lejos_oi_esferico', 8, 2)->nullable();
            $table->decimal('lejos_oi_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('lejos_oi_eje')->nullable();
            $table->string('lejos_oi_av', 30)->nullable();
            $table->string('lejos_oi_prisma', 30)->nullable();
            $table->string('lejos_oi_base', 30)->nullable();
            $table->string('lejos_oi_dnp', 30)->nullable();

            $table->text('lejos_dip')->nullable();

            // Adiciones
            $table->decimal('adicion_cerca_od', 8, 2)->nullable();
            $table->decimal('adicion_cerca_oi', 8, 2)->nullable();
            $table->decimal('adicion_intermedia_od', 8, 2)->nullable();
            $table->decimal('adicion_intermedia_oi', 8, 2)->nullable();

            // Datos de autorefractometro
            $table->string('autorefractometro_ticket_numero', 50)->nullable();
            $table->string('autorefractometro_distancia_pupilar', 30)->nullable();
            $table->json('autorefractometro_od_json')->nullable();
            $table->json('autorefractometro_oi_json')->nullable();

            // Vision de cerca
            $table->decimal('cerca_od_esferico', 8, 2)->nullable();
            $table->decimal('cerca_od_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('cerca_od_eje')->nullable();
            $table->string('cerca_od_av', 30)->nullable();
            $table->string('cerca_od_prisma', 30)->nullable();
            $table->string('cerca_od_base', 30)->nullable();
            $table->string('cerca_od_dnp', 30)->nullable();

            $table->decimal('cerca_oi_esferico', 8, 2)->nullable();
            $table->decimal('cerca_oi_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('cerca_oi_eje')->nullable();
            $table->string('cerca_oi_av', 30)->nullable();
            $table->string('cerca_oi_prisma', 30)->nullable();
            $table->string('cerca_oi_base', 30)->nullable();
            $table->string('cerca_oi_dnp', 30)->nullable();

            $table->text('cerca_dip')->nullable();

            // Vision intermedia
            $table->decimal('intermedia_od_esferico', 8, 2)->nullable();
            $table->decimal('intermedia_od_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('intermedia_od_eje')->nullable();
            $table->string('intermedia_od_av', 30)->nullable();
            $table->string('intermedia_od_prisma', 30)->nullable();
            $table->string('intermedia_od_base', 30)->nullable();
            $table->string('intermedia_od_dnp', 30)->nullable();

            $table->decimal('intermedia_oi_esferico', 8, 2)->nullable();
            $table->decimal('intermedia_oi_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('intermedia_oi_eje')->nullable();
            $table->string('intermedia_oi_av', 30)->nullable();
            $table->string('intermedia_oi_prisma', 30)->nullable();
            $table->string('intermedia_oi_base', 30)->nullable();
            $table->string('intermedia_oi_dnp', 30)->nullable();

            $table->text('intermedia_dip')->nullable();

            // Atencion y cierre
            $table->date('fecha_cita')->nullable();
            $table->date('fecha_proxima_cita')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->enum('estado', [0, 1])->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['receta_id'], 'erp_receta_graduaciones_receta_unique');
            $table->index(['fecha_cita', 'fecha_proxima_cita'], 'erp_receta_graduaciones_fechas_idx');
            $table->index(['estado', 'deleted_at'], 'erp_receta_graduaciones_estado_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_receta_graduaciones');
    }
};