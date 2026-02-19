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
        Schema::create('hr_historial_laboral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('hr_empleados')->cascadeOnDelete();

            $table->string('tipo_evento', 50);
            $table->date('fecha_evento');

            $table->foreignId('cargo_desde_id')->nullable()->constrained('hr_cargos')->nullOnDelete();
            $table->foreignId('cargo_hacia_id')->nullable()->constrained('hr_cargos')->nullOnDelete();
            $table->foreignId('departamento_desde_id')->nullable()->constrained('hr_departamentos')->nullOnDelete();
            $table->foreignId('departamento_hacia_id')->nullable()->constrained('hr_departamentos')->nullOnDelete();

            $table->text('razon')->nullable();
            $table->text('notas')->nullable();
            $table->string('referencia_documento', 100)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('empleado_id');
            $table->index('tipo_evento');
            $table->index('fecha_evento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_historial_laboral');
    }
};
