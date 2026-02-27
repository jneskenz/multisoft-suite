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
        
        // Tabla: serievisual
        Schema::create('erp_serie_visual', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30);
            $table->string('nombre', 50);
            $table->enum('estado', [0, 1])->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
                
        // Tabla: subserievisual
        Schema::create('erp_subserie_visual', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30);
            $table->string('nombre', 50);
            $table->foreignId('serie_visual_id')->nullable()->constrained('erp_serie_visual')->onDelete('set null');
            $table->enum('estado', [0, 1])->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Tabla: medida esferica
        Schema::create('erp_medida_esferica', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 50);
            $table->enum('estado', [0, 1])->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: medida cilindrica
        Schema::create('erp_medida_cilindrica', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 50);
            $table->enum('estado', [0, 1])->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: adicion
        // Schema::create('erp_adicion', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('codigo', 20)->unique();
        //     $table->string('nombre', 50);
        //     $table->foreignId('categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
        //     $table->enum('estado', [0, 1])->default(1);
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_serie_visual');
        Schema::dropIfExists('erp_subserie_visual');
        Schema::dropIfExists('erp_medida_esferica');
        Schema::dropIfExists('erp_medida_cilindrica');
        Schema::dropIfExists('erp_adicion');
    }
};
