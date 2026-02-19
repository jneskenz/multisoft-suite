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
        Schema::create('hr_empleados', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_empleado', 50)->nullable();
            $table->string('nombre', 150);
            $table->string('email', 150)->nullable();
            $table->string('documento_tipo', 50)->nullable();
            $table->string('documento_numero', 50)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('cargo', 150)->nullable();

            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_cese')->nullable();
            $table->unsignedTinyInteger('estado')->default(1);
            $table->unsignedTinyInteger('estado_empleado')->default(1)->comment('1:Activo, 2:Periodo Prueba, 3:Suspendido, 4:Cesado, 5:Vacaciones');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('core_tenants')->nullOnDelete();
            $table->foreignId('group_company_id')->nullable()->constrained('core_group_companies')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('core_companies')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('core_locations')->nullOnDelete();

            $table->unique('codigo_empleado', 'uk_hr_empleados_codigo_empleado');
            $table->unique('documento_numero', 'uk_hr_empleados_documento_numero');
            $table->unique('email', 'uk_hr_empleados_email');

            $table->index('estado');
            $table->index('group_company_id');
            $table->index('company_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_empleados');
    }
};
