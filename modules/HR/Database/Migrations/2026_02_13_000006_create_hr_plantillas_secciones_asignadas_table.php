<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_plantillas_secciones_asignadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_id')->constrained('hr_plantillas_documento')->onDelete('cascade');
            $table->foreignId('seccion_id')->constrained('hr_plantillas_secciones')->onDelete('cascade');

            // Orden y posición
            $table->integer('orden')->comment('Orden de aparición en la plantilla');
            $table->string('ubicacion', 100)->nullable()->comment('inicio, cuerpo, final');

            // Configuración
            $table->boolean('es_obligatoria')->default(false)->comment('Si es obligatoria en el documento');
            $table->boolean('es_editable')->default(true)->comment('Si se puede editar al generar el documento');

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Índices
            $table->index('plantilla_id');
            $table->index('seccion_id');
            $table->index('orden');

            // Constraint único: una sección no puede estar dos veces en la misma plantilla
            $table->unique(['plantilla_id', 'seccion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_plantillas_secciones_asignadas');
    }
};
