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
        if (!Schema::hasColumn('hr_departamentos', 'jefe_id')) {
            Schema::table('hr_departamentos', function (Blueprint $table) {
                $table->foreignId('jefe_id')->nullable()->constrained('hr_empleados')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('hr_departamentos', 'jefe_id')) {
            Schema::table('hr_departamentos', function (Blueprint $table) {
                $table->dropForeign(['jefe_id']);
                $table->dropColumn('jefe_id');
            });
        }
    }
};
