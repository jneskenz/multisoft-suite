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
        Schema::create('hr_contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('hr_empleados')->cascadeOnDelete();

            $table->string('numero_contrato', 50)->unique();
            $table->string('tipo_contrato', 50);
            $table->string('modalidad', 50)->nullable();

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('salario_base', 12, 2)->nullable();

            $table->unsignedTinyInteger('estado')->default(1);
            $table->unsignedTinyInteger('estado_contrato')->default(0)->comment('0:Borrador, 1:Enviado, 2:Firmado, 3:Rechazado, 4:Cancelado, 5:Terminado');

            $table->string('motivo_terminacion', 100)->nullable();
            $table->date('fecha_terminacion')->nullable();

            $table->string('archivo_contrato', 255)->nullable();
            $table->text('notas')->nullable();

            $table->decimal('horas_semanales', 5, 2)->nullable();
            $table->text('descripcion_horario')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('empleado_id');
            $table->index('numero_contrato');
            $table->index('estado');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_contratos');
    }
};
