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
        Schema::create('hr_categorias_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documento_id')->constrained('hr_tipos_documento')->onDelete('cascade');
            $table->string('codigo', 20)->unique()->comment('TEMP-NEC-MER, MEMO-LLAMADO, AMON-TARD');
            $table->string('nombre', 200)->comment('Nombre de la categoría');
            $table->text('descripcion')->nullable();

            // Configuración específica
            $table->boolean('requiere_justificacion')->default(false)->comment('Si necesita justificación escrita');
            $table->boolean('requiere_aprobacion')->default(false)->comment('Si necesita aprobación');
            $table->string('nivel_aprobacion', 50)->nullable()->comment('jefe_directo, gerencia, rrhh');

            // Referencias legales (si aplica)
            $table->string('articulo_ley', 50)->nullable()->comment('Ej: Art. 54 D.S. 003-97-TR');

            // Control
            $table->enum('estado', ['1', '0'])->default('1'); // 1: activo, 0: inactivo
            $table->integer('orden')->default(0);

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('codigo');
            $table->index('tipo_documento_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_categorias_documento');
    }
};
