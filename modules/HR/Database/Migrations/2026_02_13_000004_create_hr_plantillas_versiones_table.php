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
        Schema::create('hr_plantillas_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_id')->constrained('hr_plantillas_documento')->onDelete('cascade');

            // Versión
            $table->string('version', 20)->comment('Versión histórica: 1.0, 2.0, 2.1');
            $table->timestamp('fecha_version')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Contenido histórico (backup completo)
            $table->text('contenido_html')->comment('Backup del contenido HTML');
            $table->text('contenido_texto')->nullable();
            $table->json('configuracion')->nullable()->comment('Backup completo de la configuración');

            // Cambios
            $table->text('motivo_cambio')->nullable()->comment('Por qué se hizo el cambio');
            $table->text('cambios_detalle')->nullable()->comment('Descripción de qué cambió');

            // Referencias
            $table->integer('documentos_generados')->default(0)->comment('Cantidad de documentos generados con esta versión');

            // Auditoría
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes();

            // Índices
            $table->index('plantilla_id');
            $table->index('version');
            $table->index('fecha_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_plantillas_versiones');
    }
};
