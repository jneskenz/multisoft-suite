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
        Schema::create('hr_tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->comment('Código: CONT-INDEF, CERT-TRAB, MEMO-INT');
            $table->string('nombre', 100)->comment('Nombre del tipo de documento');
            $table->string('categoria', 50)->comment('contractual, administrativo, disciplinario, certificacion, liquidacion');
            $table->text('descripcion')->nullable();

            // Configuración de firmas
            $table->boolean('requiere_firma_empleado')->default(false);
            $table->boolean('requiere_firma_empleador')->default(true);
            $table->boolean('requiere_testigos')->default(false);
            $table->boolean('requiere_notarizacion')->default(false);

            // Numeración automática
            $table->boolean('usa_numeracion_automatica')->default(true);
            $table->string('prefijo_numeracion', 10)->nullable()->comment('CONT-, CERT-, MEMO-');
            $table->string('formato_numeracion', 50)->nullable()->comment('{prefijo}{año}-{numero}');

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
        Schema::dropIfExists('hr_tipos_documento');
    }
};
