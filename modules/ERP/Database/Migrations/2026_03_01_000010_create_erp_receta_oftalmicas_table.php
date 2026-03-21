<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('erp_receta_oftalmicas')) {
            return;
        }

        Schema::create('erp_receta_oftalmicas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receta_id')->constrained('erp_recetas')->cascadeOnDelete();

            $table->string('av_sc_od', 30)->nullable();
            $table->string('av_sc_oi', 30)->nullable();
            $table->string('av_cc_od', 30)->nullable();
            $table->string('av_cc_oi', 30)->nullable();
            $table->string('av_ae_od', 30)->nullable();
            $table->string('av_ae_oi', 30)->nullable();

            $table->decimal('tonometria_od', 6, 2)->nullable();
            $table->decimal('tonometria_oi', 6, 2)->nullable();
            $table->string('fondo_ojo_od', 120)->nullable();
            $table->string('fondo_ojo_oi', 120)->nullable();

            $table->text('anamnesis')->nullable();
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('antecedentes_quirurgicos')->nullable();
            $table->text('biomicroscopia_od')->nullable();
            $table->text('biomicroscopia_oi')->nullable();

            $table->string('diagnostico_od', 120)->nullable();
            $table->text('diagnostico_od_observacion')->nullable();
            $table->string('diagnostico_oi', 120)->nullable();
            $table->text('diagnostico_oi_observacion')->nullable();
            $table->string('tratamiento_od', 120)->nullable();
            $table->text('tratamiento_od_observacion')->nullable();
            $table->string('tratamiento_oi', 120)->nullable();
            $table->text('tratamiento_oi_observacion')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('estado', [0,1])->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['receta_id'], 'erp_receta_oftalmicas_receta_unique');
            $table->index(['created_at', 'deleted_at'], 'erp_receta_oftalmicas_fecha_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_receta_oftalmicas');
    }
};