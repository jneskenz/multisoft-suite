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
        Schema::create('hr_plantillas_documento', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->comment('Código único de la plantilla');
            $table->string('nombre', 200)->comment('Nombre de la plantilla');
            $table->text('descripcion')->nullable();

            // Relación con tipo y categoría
            $table->foreignId('tipo_documento_id')->constrained('hr_tipos_documento')->onDelete('cascade');
            $table->foreignId('categoria_documento_id')->nullable()->constrained('hr_categorias_documento')->onDelete('set null');

            // Contenido de la plantilla
            $table->text('contenido_html')->comment('Plantilla HTML con variables {{empleado.nombres}}, etc.');
            $table->text('contenido_texto')->nullable()->comment('Versión texto plano');

            // Configuración de formato
            $table->string('idioma', 5)->default('es')->comment('es, en');
            $table->string('formato_papel', 20)->default('A4')->comment('A4, Letter');
            $table->string('orientacion', 20)->default('vertical')->comment('vertical, horizontal');
            $table->json('margenes')->nullable()->comment('{"top": 2.5, "right": 2.5, "bottom": 2.5, "left": 2.5} en cm');

            // Variables disponibles
            $table->json('variables_disponibles')->nullable()->comment('Lista de variables que puede usar la plantilla');

            // Control de versiones
            $table->string('version', 20)->default('1.0');
            $table->boolean('es_predeterminada')->default(false)->comment('Plantilla por defecto del tipo');

            // Firma digital (puede sobrescribir la configuración del tipo)
            $table->boolean('requiere_firma_empleado')->nullable();
            $table->boolean('requiere_firma_empleador')->nullable();
            $table->boolean('requiere_testigos')->nullable();
            $table->json('posicion_firmas')->nullable()->comment('Posiciones de todas las firmas requeridas');

            // Adjuntos/Anexos
            $table->boolean('permite_anexos')->default(false);
            $table->json('anexos_requeridos')->nullable()->comment('["DNI", "Certificado médico"]');

            // Estado
            $table->enum('estado', ['1', '0'])->default('1'); // ESTADO: 1: activo, 0: inactivo

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('codigo');
            $table->index('tipo_documento_id');
            $table->index('categoria_documento_id');
            $table->index('estado');
            $table->index('version');
        });

        // Índice único para garantizar solo una plantilla predeterminada por tipo
        DB::statement('CREATE UNIQUE INDEX idx_hr_plantillas_doc_predeterminada ON hr_plantillas_documento(tipo_documento_id) WHERE es_predeterminada = true');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_plantillas_documento');
    }
};
