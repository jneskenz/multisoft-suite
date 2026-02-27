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
        if (Schema::hasTable('partners_personas')) {
            return;
        }

        Schema::create('partners_personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('core_tenants')->cascadeOnDelete();
            $table->foreignId('group_company_id')->nullable()->constrained('core_group_companies')->nullOnDelete();

            $table->string('tipo_documento', 20)->nullable();
            $table->string('numero_documento', 30)->nullable();
            $table->string('nombres', 120);
            $table->string('apellido_paterno', 120)->nullable();
            $table->string('apellido_materno', 120)->nullable();
            $table->string('nombre_completo', 255)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->boolean('estado')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'estado'], 'partners_personas_tenant_estado_idx');
            $table->index(['group_company_id', 'estado'], 'partners_personas_group_estado_idx');
            $table->index('numero_documento');
            $table->unique(['tenant_id', 'tipo_documento', 'numero_documento'], 'partners_personas_doc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_personas');
    }
};
