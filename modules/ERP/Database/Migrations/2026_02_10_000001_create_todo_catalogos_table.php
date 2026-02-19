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
        
        // Tabla: categorias
        Schema::create('erp_tipo_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50);
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
                
        // Tabla: categorias
        Schema::create('erp_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50);
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_tipo_categoria_id')->nullable()->constrained('erp_tipo_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Tabla: subcategorias
        Schema::create('erp_subcategorias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_material
        Schema::create('erp_material', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_tipos
        Schema::create('erp_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_marcas
        Schema::create('erp_marcas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_tallas
        Schema::create('erp_tallas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_colores
        Schema::create('erp_colores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_detalle_colores
        Schema::create('erp_detalle_colores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->foreignId('erp_color_id')->nullable()->constrained('erp_colores')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_clases
        Schema::create('erp_clases', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_generos
        Schema::create('erp_generos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_unidad_medidas
        Schema::create('erp_unidad_medidas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_fotocromaticos
        Schema::create('erp_fotocromaticos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_tratamientos
        Schema::create('erp_tratamientos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_indices
        Schema::create('erp_indices', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_ojobifocales
        Schema::create('erp_ojobifocales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_diametros
        Schema::create('erp_diametros', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_adiciones
        Schema::create('erp_adiciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_modalidades
        Schema::create('erp_modalidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_poderes
        Schema::create('erp_poderes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_cb
        Schema::create('erp_cb', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_o
        Schema::create('erp_o', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla: erp_modelos
        Schema::create('erp_modelos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->boolean('estado')->default(1);
            $table->foreignId('erp_categoria_id')->nullable()->constrained('erp_categorias')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_modelos');
        Schema::dropIfExists('erp_o');
        Schema::dropIfExists('erp_cb');
        Schema::dropIfExists('erp_poderes');
        Schema::dropIfExists('erp_modalidades');
        Schema::dropIfExists('erp_adiciones');
        Schema::dropIfExists('erp_diametros');
        Schema::dropIfExists('erp_ojobifocales');
        Schema::dropIfExists('erp_indices');
        Schema::dropIfExists('erp_tratamientos');
        Schema::dropIfExists('erp_fotocromaticos');
        Schema::dropIfExists('erp_unidad_medidas');
        Schema::dropIfExists('erp_generos');
        Schema::dropIfExists('erp_clases');
        Schema::dropIfExists('erp_detalle_colores');
        Schema::dropIfExists('erp_colores');
        Schema::dropIfExists('erp_tallas');
        Schema::dropIfExists('erp_marcas');
        Schema::dropIfExists('erp_tipos');
        Schema::dropIfExists('erp_materiales');
        Schema::dropIfExists('erp_subcategorias');
        Schema::dropIfExists('erp_categorias');
    }
};
