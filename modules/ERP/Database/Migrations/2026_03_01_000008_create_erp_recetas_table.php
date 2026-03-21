<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('erp_recetas')) {
            return;
        }

        Schema::create('erp_recetas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('group_company_id')->nullable();
            $table->foreignId('ticket_id')->nullable()->constrained('erp_tickets')->nullOnDelete();
            $table->unsignedBigInteger('paciente_id');
            $table->foreignId('especialista_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('receta_numero', 50)->nullable();
            $table->dateTime('fecha_receta');
            $table->string('tipo_receta', 30)->default('oftalmologica')->comment('oftalmologica, graduacion, contactologia, lentes_en_uso');
            $table->string('estado_receta', 30)->default('borrador')->comment('borrador, emitida, cerrada, anulada');
            $table->text('motivo_consulta')->nullable();
            $table->text('observaciones_generales')->nullable();
            $table->enum('estado', [0,1])->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'group_company_id'], 'erp_recetas_tenant_group_idx');
            $table->index(['paciente_id', 'fecha_receta'], 'erp_recetas_paciente_fecha_idx');
            $table->index(['ticket_id', 'estado_receta'], 'erp_recetas_ticket_estado_idx');
            $table->index(['especialista_id', 'fecha_receta'], 'erp_recetas_especialista_fecha_idx');
            $table->index(['estado', 'deleted_at'], 'erp_recetas_estado_deleted_idx');
            $table->unique(['tenant_id', 'receta_numero'], 'erp_recetas_tenant_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_recetas');
    }
};