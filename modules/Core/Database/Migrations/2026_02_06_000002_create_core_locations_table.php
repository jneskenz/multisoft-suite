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
        Schema::create('core_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('core_companies')
                ->onDelete('cascade')
                ->comment('Empresa a la que pertenece');
            
            // Identificación
            $table->string('code', 20)->nullable()->comment('Código del local');
            $table->string('name', 100)->comment('Nombre del local');
            
            // Configuración regional (puede sobrescribir de company)
            $table->string('timezone', 50)->nullable()->comment('Zona horaria (NULL = hereda de company)');
            
            // Ubicación
            $table->text('address')->nullable()->comment('Dirección');
            $table->string('city', 100)->nullable()->comment('Ciudad');
            $table->string('phone', 50)->nullable()->comment('Teléfono');
            
            // Configuración
            $table->boolean('is_main')->default(false)->comment('¿Es el local principal?');
            
            // Estado
            $table->string('status', 20)->default('active')->comment('Estado: active, inactive');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('status');
            $table->index('company_id');
            $table->index('code');
            $table->index('is_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_locations');
    }
};
