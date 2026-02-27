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
        if (Schema::hasTable('partners_empresas')) {
            return;
        }

        Schema::create('partners_empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('core_tenants')->cascadeOnDelete();
            $table->foreignId('group_company_id')->nullable()->constrained('core_group_companies')->nullOnDelete();

            $table->string('ruc', 20);
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200)->nullable();
            $table->text('direccion')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->boolean('estado')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'estado'], 'partners_empresas_tenant_estado_idx');
            $table->index(['group_company_id', 'estado'], 'partners_empresas_group_estado_idx');
            $table->unique(['tenant_id', 'ruc'], 'partners_empresas_ruc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_empresas');
    }
};
