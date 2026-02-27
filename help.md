

## Migraciones
## php artisan migrate --path=modules/HR/Database/Migrations

## Seeders
## php artisan db:seed --class=Modules\HR\Database\Seeders\DatabaseSeeder

## Routes
## php artisan route:list --name=hr.cargos


Uso rápido

## Solo revisar (sin cambiar):
## powershell -NoProfile -ExecutionPolicy Bypass -File scripts/remove-utf8-bom.ps1 -Root modules/ERP -Include *.blade.php -WhatIf

## Corregir BOM:
## powershell -NoProfile -ExecutionPolicy Bypass -File scripts/remove-utf8-bom.ps1 -Root modules/ERP -Include *.blade.php

## Para todo el proyecto BLADE + PHP + JS
## powershell -NoProfile -ExecutionPolicy Bypass -File scripts/remove-utf8-bom.ps1 -Root . -Include *.blade.php,*.php,*.js






1) Migraciones por módulo (ejecución)
php artisan migrate --path=modules/Core/Database/Migrations
php artisan migrate --path=modules/ERP/Database/Migrations
php artisan migrate --path=modules/HR/Database/Migrations
php artisan migrate --path=modules/CRM/Database/Migrations
php artisan migrate --path=modules/FMS/Database/Migrations
php artisan migrate --path=modules/Partners/Database/Migrations
php artisan migrate --path=modules/Reports/Database/Migrations

- Rollback por módulo:
php artisan migrate:rollback --path=modules/ERP/Database/Migrations --step=1

2) Seeders por módulo (ejecución)
php artisan db:seed --class="Modules\Core\Database\Seeders\DatabaseSeeder"
php artisan db:seed --class="Modules\HR\Database\Seeders\DatabaseSeeder"







## ESTADO DEL SISTEMA

A) Catálogos transversales (para casi todas las tablas)

Estos los puedes reutilizar en cualquier entidad (solicitudes, documentos, expedientes, movimientos).

1) record_status (ciclo de vida del registro)

0 BORRADOR

1 PENDIENTE

2 EN_REVISION

3 OBSERVADO

4 APROBADO

5 RECHAZADO

8 ANULADO

9 ARCHIVADO (solo lectura)

Notas RRHH:

Yo evitaría usar ELIMINADO para cosas laborales por trazabilidad/retención; mejor ARCHIVADO + auditoría.

PUBLICADO y FINALIZADO solo si realmente hay “publicación” o cierre formal del trámite.

2) enable_status (habilitación operativa)

Útil para “se puede usar / no se puede usar” (por ejemplo: plantilla activa, concepto de nómina habilitado, etc.).

0 DESHABILITADO

1 HABILITADO

2 SUSPENDIDO

3) approval_status (si hay aprobaciones formales)

0 NO_REQUIERE

1 POR_APROBAR

2 APROBADO

3 DENEGADO

4 VENCIDO (opcional)

5 REVOCADO (si se revierte)

4) process_status (tareas/colas/integraciones)

0 NO_INICIADO

1 EN_PROCESO

2 COMPLETADO

3 FALLIDO

4 REINTENTANDO

5 PAUSADO

6 CANCELADO

B) Catálogos propios de RRHH (los más importantes)
1) employment_status (situación laboral del colaborador)

Este no debe confundirse con record_status. Describe la relación laboral real.

Recomendado:

0 PRE_INGRESO (seleccionado / por iniciar, opcional)

1 ACTIVO

2 EN_LICENCIA (vacaciones, descanso médico, maternidad, etc.)

3 SUSPENDIDO (medida disciplinaria o similar, opcional)

4 CESADO (fin de relación laboral)

5 RETIRADO/JUBILADO (opcional si aplica)

6 FALLECIDO (opcional; depende de políticas y privacidad)

Tip: si quieres más detalle, no metas 20 estados aquí: usa employment_status + leave_type o separation_reason.

2) contract_status (contrato / vínculo contractual)

0 BORRADOR

1 POR_FIRMAR

2 VIGENTE

3 POR_VENCER (opcional para alertas)

4 VENCIDO

5 RESUELTO/TERMINADO

6 ANULADO/CANCELADO

3) recruitment_status (reclutamiento / postulaciones)

Para el “pipeline” (vacante o candidato).

0 ABIERTO

1 EN_FILTRO (CV/shortlist)

2 EN_ENTREVISTAS

3 EN_EVALUACION (pruebas / referencias)

4 OFERTA_ENVIADA

5 CONTRATADO

6 RECHAZADO

7 DESISTIO (candidato se retira)

8 EN_PAUSA (opcional)

9 CERRADO

4) onboarding_status (inducción / ingreso)

0 NO_INICIADO

1 EN_PROCESO

2 COMPLETADO

3 CANCELADO

5) leave_request_status (solicitudes: vacaciones/licencias/permisos)

0 BORRADOR

1 ENVIADO

2 EN_REVISION

3 APROBADO

4 RECHAZADO

5 ANULADO/CANCELADO

6 EJECUTADO/TOMADO (opcional)

7 CERRADO (opcional)

(Aquí también suele ir approval_status, pero si ya lo tienes aparte, puedes simplificar este catálogo.)

6) payroll_run_status (corrida de nómina)

0 PREPARADO

1 CALCULANDO

2 EN_VALIDACION

3 APROBADO_PARA_PAGO

4 PAGADO

5 OBSERVADO (ajustes requeridos)

6 ANULADO/REVERSADO

7 CERRADO

7) performance_cycle_status (evaluación de desempeño)

0 NO_INICIADO

1 AUTOEVALUACION

2 EVALUACION_JEFATURA

3 CALIBRACION (opcional)

4 CERRADO

Recomendación práctica para RRHH

Mantén 4 catálogos transversales (A) y agrega solo los de negocio que uses (B).

Para no explotar estados: agrega siempre un status_reason (código + texto) en “OBSERVADO / RECHAZADO / SUSPENDIDO / FALLIDO / CESADO”.

Si me dices qué módulos exactos tendrás (por ejemplo: legajo, contratos, vacaciones/licencias, nómina, asistencia, desempeño), te lo dejo “cerrado” en una lista final por entidad (Empleado, Contrato, Solicitud, Documento, Corrida de nómina, etc.) con transiciones recomendadas.