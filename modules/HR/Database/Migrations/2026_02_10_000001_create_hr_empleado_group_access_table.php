<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_empleado_group_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('group_company_id');
            $table->timestamps();
            $table->foreign('empleado_id')->references('id')->on('hr_empleados')->onDelete('cascade');
            $table->foreign('group_company_id')->references('id')->on('core_group_companies')->onDelete('cascade');
            $table->unique(['empleado_id', 'group_company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_empleado_group_access');
    }
};
