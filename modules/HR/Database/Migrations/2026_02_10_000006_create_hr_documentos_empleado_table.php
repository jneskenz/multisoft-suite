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
        Schema::create('hr_documentos_empleado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('hr_empleados')->cascadeOnDelete();

            $table->string('tipo_documento', 50);
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();

            $table->string('nombre_archivo', 255);
            $table->string('ruta_archivo', 500);
            $table->unsignedBigInteger('tamano_archivo')->nullable();
            $table->string('tipo_mime', 100)->nullable();

            $table->timestamp('fecha_subida')->useCurrent();
            $table->date('fecha_vencimiento')->nullable();

            $table->boolean('esta_verificado')->default(false);
            $table->foreignId('verificado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verificado_en')->nullable();

            $table->unsignedTinyInteger('estado')->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('empleado_id');
            $table->index('tipo_documento');
            $table->index('fecha_vencimiento');
            $table->index('esta_verificado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_documentos_empleado');
    }
};
