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
        if (Schema::hasTable('partners_persona_empresa')) {
            return;
        }

        Schema::create('partners_persona_empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('partners_personas')->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained('partners_empresas')->cascadeOnDelete();
            $table->string('tipo_relacion', 40)->default('contacto');
            $table->boolean('es_principal')->default(false);
            $table->boolean('estado')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['empresa_id', 'estado'], 'partners_persona_empresa_empresa_estado_idx');
            $table->index(['persona_id', 'es_principal'], 'partners_persona_empresa_persona_principal_idx');
            $table->unique(['persona_id', 'empresa_id'], 'partners_persona_empresa_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_persona_empresa');
    }
};
