<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hr_empleados', function (Blueprint $table) {
            // 1. Agregar relación opcional con users
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // 2. Eliminar columna password (ya no se usa)
            $table->dropColumn('password');
            
            // 3. Modificar email para que no sea único (solo para contacto)
            $table->dropUnique(['email']); // Eliminar constraint unique
            
            // 4. Agregar campos de identificación personal
            $table->string('documento_tipo', 20)->nullable()->after('email');
            $table->string('documento_numero', 20)->nullable()->after('documento_tipo');
            $table->string('telefono', 20)->nullable()->after('documento_numero');
            
            // 5. Agregar jerarquía organizacional
            $table->unsignedBigInteger('group_company_id')->nullable()->after('tenant_id');
            $table->unsignedBigInteger('company_id')->nullable()->after('group_company_id');
            $table->unsignedBigInteger('location_id')->nullable()->after('company_id');
            
            // 6. Agregar información laboral
            $table->string('codigo_empleado', 20)->nullable()->after('telefono');
            $table->string('cargo', 100)->nullable()->after('codigo_empleado');
            $table->date('fecha_ingreso')->nullable()->after('cargo');
            $table->date('fecha_cese')->nullable()->after('fecha_ingreso');
            
            // 7. Agregar foreign keys para jerarquía
            $table->foreign('group_company_id')->references('id')->on('core_group_companies')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('core_companies')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('core_locations')->onDelete('set null');
            
            // 8. Agregar unique constraints
            $table->unique('documento_numero', 'uk_empleado_documento');
            $table->unique('codigo_empleado', 'uk_empleado_codigo');
            
            // 9. Agregar índices para performance
            $table->index('user_id', 'idx_empleados_user');
            $table->index('group_company_id', 'idx_empleados_group');
            $table->index('company_id', 'idx_empleados_company');
            $table->index('location_id', 'idx_empleados_location');
            $table->index('documento_numero', 'idx_empleados_documento');
            $table->index('codigo_empleado', 'idx_empleados_codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_empleados', function (Blueprint $table) {
            // Eliminar foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['group_company_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['location_id']);
            
            // Eliminar índices
            $table->dropIndex('idx_empleados_user');
            $table->dropIndex('idx_empleados_group');
            $table->dropIndex('idx_empleados_company');
            $table->dropIndex('idx_empleados_location');
            $table->dropIndex('idx_empleados_documento');
            $table->dropIndex('idx_empleados_codigo');
            
            // Eliminar unique constraints
            $table->dropUnique('uk_empleado_documento');
            $table->dropUnique('uk_empleado_codigo');
            
            // Eliminar columnas nuevas
            $table->dropColumn([
                'user_id',
                'documento_tipo',
                'documento_numero',
                'telefono',
                'group_company_id',
                'company_id',
                'location_id',
                'codigo_empleado',
                'cargo',
                'fecha_ingreso',
                'fecha_cese',
            ]);
            
            // Restaurar password
            $table->string('password')->after('email');
            
            // Restaurar unique en email
            $table->unique('email');
        });
    }
};
