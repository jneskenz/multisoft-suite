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
        Schema::create('hr_documentos_generados', function (Blueprint $table) {
            $table->id();

            // Identificación del documento
            $table->string('numero_documento', 50)->unique()->comment('CONT-2026-001, CERT-2026-045');
            $table->foreignId('tipo_documento_id')->constrained('hr_tipos_documento')->onDelete('restrict');
            $table->foreignId('categoria_documento_id')->nullable()->constrained('hr_categorias_documento')->onDelete('set null');

            // Plantilla utilizada
            $table->foreignId('plantilla_utilizada_id')->nullable()->constrained('hr_plantillas_documento')->onDelete('set null');
            $table->string('plantilla_version', 20)->nullable();

            // Contenido generado
            $table->text('contenido_generado')->nullable()->comment('HTML final con todas las variables reemplazadas');
            $table->json('variables_aplicadas')->nullable()->comment('Valores usados: {"empleado": {"nombres": "Joel"}}');
            $table->timestamp('fecha_generacion')->nullable();

            // Relaciones con otras entidades
            $table->foreignId('empleado_id')->nullable()->constrained('hr_empleados')->onDelete('restrict');
            $table->foreignId('contrato_id')->nullable()->constrained('hr_contratos')->onDelete('set null');
            $table->string('relacionado_con_tipo', 50)->nullable()->comment('empleado, contrato, departamento, etc.');
            $table->bigInteger('relacionado_con_id')->nullable()->comment('ID de la entidad relacionada');

            // Firmas y aprobaciones
            $table->string('estado_firmas', 20)->default('pendiente')->comment('pendiente, firmado, rechazado');
            $table->timestamp('firmado_por_empleado_at')->nullable();
            $table->timestamp('firmado_por_empleador_at')->nullable();
            $table->json('firmas_digitales')->nullable()->comment('Información de firmas digitales');

            // Archivos adjuntos
            $table->string('ruta_archivo_pdf', 500)->nullable()->comment('PDF generado');
            $table->json('archivos_anexos')->nullable()->comment('Lista de anexos adjuntos');

            // Estado del documento
            $table->enum('estado', ['1', '0'])->default('1')->comment('1: activo, 0: inactivo');
            $table->enum('estado_documento', ['0', '1', '2', '3', '4', '5', '6'])
                ->default('0')
                ->comment('0:borrador, 1:por_firmar, 2:vigente, 3:por_vencer, 4:vencido, 5:anulado, 6:resuelto');

            $table->date('fecha_vigencia_desde')->nullable();
            $table->date('fecha_vigencia_hasta')->nullable();
            $table->text('motivo_anulacion')->nullable();
            $table->foreignId('anulado_por')->nullable()->constrained('users');
            $table->timestamp('anulado_at')->nullable();

            // Observaciones
            $table->text('observaciones')->nullable();

            // Auditoría
            $table->foreignId('creado_por')->constrained('users');
            $table->foreignId('actualizado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('numero_documento');
            $table->index('tipo_documento_id');
            $table->index('categoria_documento_id');
            $table->index('empleado_id');
            $table->index('contrato_id');
            $table->index('estado');
            $table->index('estado_firmas');
            $table->index('fecha_generacion');
            $table->index(['relacionado_con_tipo', 'relacionado_con_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_documentos_generados');
    }
};
