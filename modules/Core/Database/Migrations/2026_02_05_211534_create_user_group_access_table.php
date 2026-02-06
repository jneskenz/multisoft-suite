<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla pivot para controlar qué grupos puede acceder cada usuario.
     * Un usuario puede tener acceso a múltiples grupos del mismo tenant.
     */
    public function up(): void
    {
        Schema::create('core_user_group_access', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            
            $table->foreignId('group_company_id')
                ->constrained('core_group_companies')
                ->cascadeOnDelete();
            
            // Timestamps
            $table->timestamps();
            
            // Un usuario solo puede tener un registro por grupo
            $table->unique(['user_id', 'group_company_id'], 'user_group_unique');
            
            // Índices para consultas frecuentes
            $table->index('user_id');
            $table->index('group_company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_user_group_access');
    }
};
