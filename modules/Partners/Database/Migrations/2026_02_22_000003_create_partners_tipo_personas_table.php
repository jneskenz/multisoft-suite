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
        if (Schema::hasTable('partners_tipo_personas')) {
            return;
        }

        Schema::create('partners_tipo_personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('partners_personas')->cascadeOnDelete();
            $table->string('tipo', 30)->comment('cliente | proveedor | paciente');
            $table->boolean('estado')->default(true);
            $table->string('observacion', 255)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['tipo', 'estado'], 'partners_tipo_personas_tipo_estado_idx');
            $table->unique(['persona_id', 'tipo'], 'partners_tipo_personas_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_tipo_personas');
    }
};
