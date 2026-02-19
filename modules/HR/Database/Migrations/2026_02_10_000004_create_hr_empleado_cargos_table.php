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
        Schema::create('hr_empleado_cargos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empleado_id')->constrained('hr_empleados')->cascadeOnDelete();
            $table->foreignId('cargo_id')->constrained('hr_cargos')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('hr_departamentos')->cascadeOnDelete();
            $table->foreignId('reporta_a')->nullable()->constrained('hr_empleados')->nullOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('es_actual')->default(true);
            $table->unsignedTinyInteger('estado')->default(1);
            $table->text('notas')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('empleado_id');
            $table->index('cargo_id');
            $table->index('departamento_id');
            $table->index('estado');
            $table->index(['empleado_id', 'es_actual']);
            $table->unique(['empleado_id', 'cargo_id', 'fecha_inicio'], 'uk_hr_empleado_cargos_historial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_empleado_cargos');
    }
};
