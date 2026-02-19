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
        Schema::create('hr_plantillas_secciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->comment('Código: SEC-ENC-EMPRESA, SEC-CONF');
            $table->string('nombre', 200)->comment('Nombre de la sección');
            $table->text('descripcion')->nullable();

            // Contenido
            $table->text('contenido_html')->comment('Contenido HTML de la sección');
            $table->text('contenido_texto')->nullable()->comment('Versión texto plano');

            // Categorización
            $table->string('categoria', 50)->nullable()->comment('encabezado, cuerpo, clausula, footer, firma');

            // Aplicable a qué tipos de documentos
            $table->json('aplicable_a')->nullable()->comment('["CONT", "CERT", "MEMO"] - tipos donde se puede usar');

            $table->boolean('es_obligatoria')->default(false)->comment('Si debe incluirse siempre');

            // Variables que usa
            $table->json('variables_usadas')->nullable()->comment('["empleado.nombres", "empresa.razon_social"]');

            // Estado
            $table->enum('estado', ['1', '0'])->default('1'); // 1: activo, 0: inactivo
            $table->integer('orden')->default(0);

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('codigo');
            $table->index('categoria');
            $table->index('estado');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_plantillas_secciones');
    }
};
