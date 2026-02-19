<?php

namespace Modules\HR\Enums;

enum EstadoContrato: int
{
   case BORRADOR = 0;
   case REVISION = 1;
   case FIRMADO = 2;
   case RECHAZADO = 3;
   case CANCELADO = 4;
   case TERMINADO = 5;
   case VENCIDO = 6;
   case OBSERVADO = 7;

   public function label(): string
   {
      return match ($this) {
         self::BORRADOR => 'Borrador',
         self::REVISION => 'En revisión / Por Firmar',
         self::FIRMADO => 'Aprobado / Firmado',
         self::RECHAZADO => 'Rechazado',
         self::CANCELADO => 'Cancelado',
         self::TERMINADO => 'Terminado / Finalizado',
         self::VENCIDO => 'Vencido',
         self::OBSERVADO => 'Observado',
      };
   }

   public function color(): string
   {
      return match ($this) {
         self::BORRADOR => 'secondary',
         self::REVISION => 'info',
         self::FIRMADO => 'success',
         self::RECHAZADO => 'danger',
         self::CANCELADO => 'warning',
         self::TERMINADO => 'dark',
         self::VENCIDO => 'danger',
         self::OBSERVADO => 'warning',
      };
   }

   public function icon(): string
   {
      return match ($this) {
         self::BORRADOR => 'ri-draft-line',
         self::REVISION => 'ri-send-plane-line',
         self::FIRMADO => 'ri-checkbox-circle-line',
         self::RECHAZADO => 'ri-close-circle-line',
         self::CANCELADO => 'ri-forbid-line',
         self::TERMINADO => 'ri-flag-line',
         self::VENCIDO => 'ri-flag-line',
         self::OBSERVADO => 'ri-flag-line',
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
