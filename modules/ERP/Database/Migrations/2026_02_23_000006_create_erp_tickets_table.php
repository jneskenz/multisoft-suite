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
        if (Schema::hasTable('erp_tickets')) {
            return;
        }

        Schema::create('erp_tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained('core_tenants')->cascadeOnDelete();
            $table->foreignId('group_company_id')->nullable()->constrained('core_group_companies')->nullOnDelete();
            $table->foreignId('paciente_id')->nullable()->constrained('partners_personas')->nullOnDelete();

            $table->string('ticket_numero', 30);
            $table->dateTime('fecha_ticket');
            $table->string('estado_ticket', 30)->default('abierto');
            $table->string('prioridad', 20)->default('normal');
            $table->string('canal', 30)->nullable();
            $table->text('resumen')->nullable();

            $table->string('moneda', 3)->default('PEN');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_total', 12, 2)->default(0);
            $table->decimal('impuesto_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);

            $table->dateTime('fecha_cierre')->nullable();
            $table->foreignId('cerrado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('estado', [1, 0])->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'ticket_numero'], 'erp_tickets_tenant_numero_unique');
            $table->index(['tenant_id', 'estado_ticket', 'fecha_ticket'], 'erp_tickets_tenant_estado_fecha_idx');
            $table->index(['paciente_id', 'estado_ticket'], 'erp_tickets_paciente_estado_idx');
            $table->index('fecha_ticket');
            $table->index(['group_company_id', 'estado'], 'erp_tickets_group_estado_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_tickets');
    }
};
