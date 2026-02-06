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
        Schema::create('core_settings', function (Blueprint $table) {
            $table->id();
            
            // Scope (herencia: Tenant → Group → Company → Location)
            $table->foreignId('tenant_id')
                ->constrained('core_tenants')
                ->onDelete('cascade')
                ->comment('Tenant al que pertenece');
            $table->foreignId('group_company_id')
                ->nullable()
                ->constrained('core_group_companies')
                ->onDelete('cascade')
                ->comment('Grupo empresa (NULL = nivel tenant)');
            $table->unsignedBigInteger('company_id')
                ->nullable()
                ->comment('Empresa ERP (NULL = nivel grupo)');
            $table->unsignedBigInteger('location_id')
                ->nullable()
                ->comment('Local/Sede (NULL = nivel empresa)');
            
            // Setting
            $table->string('category', 50)->comment('Categoría: security, notifications, appearance, mail');
            $table->string('key', 100)->comment('Clave del setting');
            $table->text('value')->nullable()->comment('Valor almacenado');
            $table->string('type', 20)->default('string')->comment('Tipo: string, boolean, integer, json, array');
            
            // Metadata
            $table->string('display_name', 150)->nullable()->comment('Nombre para mostrar');
            $table->text('description')->nullable()->comment('Descripción del setting');
            
            $table->timestamps();
            
            // Una sola configuración por scope+category+key
            $table->unique(
                ['tenant_id', 'group_company_id', 'company_id', 'location_id', 'category', 'key'],
                'unique_setting_scope'
            );
            
            // Índices
            $table->index(['tenant_id', 'category']);
            $table->index(['group_company_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_settings');
    }
};
