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
        Schema::create('core_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_company_id')
                ->constrained('core_group_companies')
                ->onDelete('cascade')
                ->comment('Grupo al que pertenece');
            
            // Identificación
            $table->string('code', 20)->nullable()->comment('Código de la empresa');
            $table->string('name', 150)->comment('Razón social');
            $table->string('trade_name', 150)->nullable()->comment('Nombre comercial');
            $table->string('tax_id', 20)->nullable()->comment('RUC de esta empresa específica');
            
            // Configuración regional (puede sobrescribir del grupo)
            $table->string('timezone', 50)->nullable()->comment('Zona horaria (NULL = hereda del grupo)');
            
            // Contacto
            $table->text('address')->nullable()->comment('Dirección');
            $table->string('phone', 50)->nullable()->comment('Teléfono');
            $table->string('email', 100)->nullable()->comment('Email');
            
            // Estado
            $table->string('status', 20)->default('active')->comment('Estado: active, inactive');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('status');
            $table->index('group_company_id');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_companies');
    }
};
