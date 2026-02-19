<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
    * tipo_contrato  : string → unsignedBigInteger FK → hr_tipos_documento
    * modalidad      : string → unsignedBigInteger FK → hr_categorias_documento
    *
    * Strategy: add new FK columns first, migrate existing data, then drop old columns.
    */
   public function up(): void
   {
      // 1) Add new FK columns alongside old ones
      Schema::table('hr_contratos', function (Blueprint $table) {
         $table->unsignedBigInteger('tipo_contrato_id')
            ->nullable()          // temporary — allows data migration
            ->after('numero_contrato');

         $table->unsignedBigInteger('modalidad_id')
            ->nullable()
            ->after('tipo_contrato_id');
      });

      // 2) Migrate existing rows: map old string values to FK ids
      $mapping = DB::table('hr_tipos_documento')
         ->where('categoria', 'contractual')
         ->pluck('id', 'codigo');

      // Map common string values to tipo_documento codes
      $stringToCode = [
         'indefinido'  => 'CONT-INDEF',
         'plazo_fijo'  => 'CONT-TEMP',
         'temporal'    => 'CONT-TEMP',
         'practicas'   => 'CONT-PRAC',
      ];

      foreach ($stringToCode as $oldValue => $code) {
         if (isset($mapping[$code])) {
            DB::table('hr_contratos')
               ->where('tipo_contrato', $oldValue)
               ->update(['tipo_contrato_id' => $mapping[$code]]);
         }
      }

      // Any remaining rows without a match → set to first contractual tipo
      $defaultId = $mapping->first();
      if ($defaultId) {
         DB::table('hr_contratos')
            ->whereNull('tipo_contrato_id')
            ->update(['tipo_contrato_id' => $defaultId]);
      }

      // 3) Drop old string columns
      Schema::table('hr_contratos', function (Blueprint $table) {
         $table->dropColumn(['tipo_contrato', 'modalidad']);
      });

      // 4) Make tipo_contrato_id NOT NULL + add FK constraints + index
      Schema::table('hr_contratos', function (Blueprint $table) {
         // Can't change nullable with change() on certain drivers,
         // so we just add the FK constraint
         $table->foreign('tipo_contrato_id')
            ->references('id')
            ->on('hr_tipos_documento')
            ->comment('FK a hr_tipos_documento (contractual)');

         $table->foreign('modalidad_id')
            ->references('id')
            ->on('hr_categorias_documento')
            ->comment('FK a hr_categorias_documento');

         $table->index(['tipo_contrato_id', 'modalidad_id']);
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::table('hr_contratos', function (Blueprint $table) {
         $table->dropForeign(['tipo_contrato_id']);
         $table->dropForeign(['modalidad_id']);
         $table->dropIndex(['tipo_contrato_id', 'modalidad_id']);
         $table->dropColumn(['tipo_contrato_id', 'modalidad_id']);
      });

      Schema::table('hr_contratos', function (Blueprint $table) {
         $table->string('tipo_contrato', 50)->after('numero_contrato');
         $table->string('modalidad', 50)->nullable()->after('tipo_contrato');
      });
   }
};
