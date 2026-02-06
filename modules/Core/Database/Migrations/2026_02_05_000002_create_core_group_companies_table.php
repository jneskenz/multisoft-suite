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
        Schema::create('core_group_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('core_tenants')
                ->onDelete('cascade')
                ->comment('Tenant al que pertenece');
            
            // Identificación
            $table->string('code', 20)->comment('Código del grupo (PE, EC, CO)');
            $table->char('country_code', 2)->comment('Código ISO del país (PE, EC, CO)');
            
            // Datos legales en este país
            $table->string('business_name', 150)->comment('Razón social en este país');
            $table->string('trade_name', 150)->nullable()->comment('Nombre comercial');
            $table->string('tax_id', 30)->nullable()->comment('RUC/NIT local');
            $table->string('tax_id_type', 20)->default('RUC')->comment('Tipo de documento fiscal');
            
            // Branding (puede sobrescribir del tenant)
            $table->string('app_name', 100)->nullable()->comment('Nombre personalizado de la app');
            $table->string('logo', 255)->nullable()->comment('Logo específico del grupo');
            $table->string('favicon', 255)->nullable()->comment('Favicon específico del grupo');
            
            // Contacto en este país
            $table->text('address')->nullable()->comment('Dirección principal');
            $table->string('city', 100)->nullable()->comment('Ciudad');
            $table->string('phone', 50)->nullable()->comment('Teléfono');
            $table->string('email', 100)->nullable()->comment('Email de contacto');
            $table->string('website', 150)->nullable()->comment('Sitio web');
            
            // Configuración regional base
            $table->string('timezone', 50)->default('America/Lima')->comment('Zona horaria');
            $table->string('locale', 10)->default('es')->comment('Idioma por defecto');
            $table->char('currency_code', 3)->default('PEN')->comment('Código de moneda');
            $table->string('currency_symbol', 10)->default('S/')->comment('Símbolo de moneda');
            $table->string('date_format', 20)->default('d/m/Y')->comment('Formato de fecha');
            $table->string('time_format', 20)->default('H:i')->comment('Formato de hora');
            $table->char('decimal_separator', 1)->default('.')->comment('Separador decimal');
            $table->char('thousands_separator', 1)->default(',')->comment('Separador de miles');
            
            // Estado
            $table->string('status', 20)->default('active')->comment('Estado: active, inactive');
            
            $table->timestamps();
            
            // Constraints
            $table->unique(['tenant_id', 'country_code'], 'unique_tenant_country');
            
            // Índices
            $table->index('status');
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_group_companies');
    }
};
