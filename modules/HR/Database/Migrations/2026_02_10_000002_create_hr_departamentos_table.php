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
        Schema::create('hr_departamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_departamento_id')->nullable()->constrained('hr_tipo_departamentos')->nullOnDelete();
            $table->foreignId('padre_id')->nullable()->constrained('hr_departamentos')->nullOnDelete();

            $table->string('codigo', 50)->nullable();
            $table->string('name', 100);
            $table->text('descripcion')->nullable();
            $table->unsignedTinyInteger('estado')->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('codigo', 'uk_hr_departamentos_codigo');
            $table->index('estado');
            $table->index('tipo_departamento_id');
            $table->index('padre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_departamentos');
    }
};
