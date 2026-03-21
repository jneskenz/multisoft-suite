<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('erp_receta_lentes_en_uso')) {
            return;
        }

        Schema::create('erp_receta_lentes_en_uso', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receta_id')->constrained('erp_recetas')->cascadeOnDelete();

            $table->decimal('od_esferico', 8, 2)->nullable();
            $table->decimal('od_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('od_eje')->nullable();
            $table->string('od_av_cc', 30)->nullable();
            $table->string('od_altura', 30)->nullable();
            $table->decimal('od_adicion', 8, 2)->nullable();

            $table->decimal('oi_esferico', 8, 2)->nullable();
            $table->decimal('oi_cilindro', 8, 2)->nullable();
            $table->unsignedSmallInteger('oi_eje')->nullable();
            $table->string('oi_av_cc', 30)->nullable();
            $table->string('oi_altura', 30)->nullable();
            $table->decimal('oi_adicion', 8, 2)->nullable();

            $table->text('dip')->nullable();
            $table->boolean('usa_lejos')->default(false);
            $table->boolean('usa_cerca')->default(false);
            $table->text('observaciones')->nullable();
            $table->enum('estado', [0, 1])->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['receta_id'], 'erp_receta_lentes_en_uso_receta_unique');
            $table->index(['estado', 'deleted_at'], 'erp_receta_lentes_en_uso_estado_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_receta_lentes_en_uso');
    }
};