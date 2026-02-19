<?php

namespace Modules\HR\Enums;

enum EstadoEmpleado: int
{
   case ACTIVO = 1;
   case PERIODO_PRUEBA = 2;
   case SUSPENDIDO = 3;
   case CESADO = 4;
   case VACACIONES = 5;

   public function label(): string
   {
      return match ($this) {
         self::ACTIVO => 'Activo',
         self::PERIODO_PRUEBA => 'En Periodo de Prueba',
         self::SUSPENDIDO => 'Suspendido',
         self::CESADO => 'Cesado',
         self::VACACIONES => 'Vacaciones',
      };
   }

   public function color(): string
   {
      return match ($this) {
         self::ACTIVO => 'success',
         self::PERIODO_PRUEBA => 'info',
         self::SUSPENDIDO => 'warning',
         self::CESADO => 'danger',
         self::VACACIONES => 'secondary',
      };
   }

   public function icon(): string
   {
      return match ($this) {
         self::ACTIVO => 'ri-checkbox-circle-line',
         self::PERIODO_PRUEBA => 'ri-time-line',
         self::SUSPENDIDO => 'ri-pause-circle-line',
         self::CESADO => 'ri-close-circle-line',
         self::VACACIONES => 'ri-sun-line',
      };
   }

   /**
    * Obtener todas las opciones para selects.
    */
   public static function options(): array
   {
      return array_map(
         fn(self $case) => ['value' => $case->value, 'label' => $case->label()],
         self::cases()
      );
   }
}
