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
        if (!Schema::hasTable('erp_categorias')) {
            return;
        }

        $addCamposAutocompletado = !Schema::hasColumn('erp_categorias', 'campos_autocompletado');
        $addCaracteristicas = !Schema::hasColumn('erp_categorias', 'caracteristicas');

        if ($addCamposAutocompletado || $addCaracteristicas) {
            Schema::table('erp_categorias', function (Blueprint $table) use ($addCamposAutocompletado, $addCaracteristicas) {
                // Guarda el arreglo de campos usados para autocompletar descripcion.
                if ($addCamposAutocompletado) {
                    $table->json('campos_autocompletado')->nullable();
                }

                // Guarda los campos dinamicos que se renderizan para la categoria.
                if ($addCaracteristicas) {
                    $table->json('caracteristicas')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('erp_categorias')) {
            return;
        }

        $dropCamposAutocompletado = Schema::hasColumn('erp_categorias', 'campos_autocompletado');
        $dropCaracteristicas = Schema::hasColumn('erp_categorias', 'caracteristicas');

        if ($dropCamposAutocompletado || $dropCaracteristicas) {
            Schema::table('erp_categorias', function (Blueprint $table) use ($dropCamposAutocompletado, $dropCaracteristicas) {
                if ($dropCamposAutocompletado) {
                    $table->dropColumn('campos_autocompletado');
                }

                if ($dropCaracteristicas) {
                    $table->dropColumn('caracteristicas');
                }
            });
        }
    }
};
