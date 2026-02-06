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
        Schema::create('core_tenants', function (Blueprint $table) {
            $table->id();
            
            // Identificación
            $table->string('code', 20)->unique()->comment('Código único del tenant (TNT-001)');
            $table->string('slug', 50)->unique()->nullable()->comment('Slug para subdominio futuro');
            
            // Datos del cliente contratante
            $table->string('name', 150)->comment('Nombre comercial del cliente');
            $table->string('legal_name', 200)->nullable()->comment('Razón social legal');
            $table->string('tax_id', 30)->nullable()->comment('RUC/NIT de la matriz principal');
            
            // Contacto principal (dueño/representante)
            $table->string('contact_name', 100)->nullable()->comment('Nombre del contacto principal');
            $table->string('contact_email', 100)->nullable()->comment('Email del contacto principal');
            $table->string('contact_phone', 50)->nullable()->comment('Teléfono del contacto principal');
            
            // Plan/Suscripción
            $table->string('plan', 50)->default('basic')->comment('Plan contratado: basic, standard, premium, enterprise');
            $table->string('billing_cycle', 20)->nullable()->comment('Ciclo de facturación: monthly, yearly');
            $table->unsignedInteger('max_users')->default(10)->comment('Máximo de usuarios permitidos');
            $table->unsignedInteger('max_group_companies')->default(1)->comment('Máximo de grupos/países permitidos');
            $table->unsignedInteger('max_companies')->default(5)->comment('Máximo de empresas permitidas');
            $table->unsignedInteger('max_locations')->default(20)->comment('Máximo de locales permitidos');
            $table->json('modules_enabled')->nullable()->comment('Módulos habilitados: ["core","erp","crm"]');
            
            // Vigencia
            $table->timestamp('trial_ends_at')->nullable()->comment('Fecha fin del período de prueba');
            $table->date('subscription_starts_at')->nullable()->comment('Fecha inicio de suscripción');
            $table->date('subscription_ends_at')->nullable()->comment('Fecha fin de suscripción');
            
            // Branding global
            $table->string('logo', 255)->nullable()->comment('Ruta del logo');
            $table->string('favicon', 255)->nullable()->comment('Ruta del favicon');
            $table->string('primary_color', 20)->nullable()->comment('Color primario de la marca');
            
            // Estado
            $table->string('status', 20)->default('active')->comment('Estado: active, trial, suspended, cancelled');
            
            $table->timestamps();
            
            // Índices
            $table->index('status');
            $table->index('plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_tenants');
    }
};
