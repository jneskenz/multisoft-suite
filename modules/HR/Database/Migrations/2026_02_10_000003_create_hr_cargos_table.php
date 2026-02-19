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
        Schema::create('hr_cargos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('hr_departamentos')->cascadeOnDelete();

            $table->string('codigo', 50)->nullable();
            $table->string('name', 100);
            $table->text('descripcion')->nullable();
            $table->string('nivel', 50)->nullable();
            $table->unsignedTinyInteger('estado')->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['departamento_id', 'codigo'], 'uk_hr_cargos_departamento_codigo');
            $table->index('estado');
            $table->index('departamento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_cargos');
    }
};
