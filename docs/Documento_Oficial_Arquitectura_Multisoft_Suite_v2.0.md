# Documento Oficial de Arquitectura

# Multisoft Suite

**Suite Empresarial Completa: ERP + CRM + RRHH + FMS + Módulos Extensibles**

Laravel 12 + Livewire 4 + PostgreSQL + Bootstrap 5

Dominio: multisoft.pe | Modelo: Monoempresa por Proyecto

*Versión: 2.0 | Fecha: 03/02/2026*

---

# Historial de Cambios

| Versión | Fecha | Descripción |
|---------|-------|-------------|
| 1.0 | 25/01/2026 | Versión inicial de arquitectura |
| 1.1 | 31/01/2026 | Adiciones de notificaciones, workflows, caché |
| 1.2 | 31/01/2026 | Sistema de menús dinámicos |
| **2.0** | **03/02/2026** | **Unificación completa: Sistema de idiomas, Rol único global, Estructura modular consolidada** |

---

# Resumen Ejecutivo

| Propósito | Definir una arquitectura sólida, robusta y escalable para construir una suite empresarial completa tipo ERP/CRM/RRHH/FMS con módulos instalables y extensibles. |
|-----------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Alcance | Arquitectura modular por paquetes, enrutamiento multiidioma, RBAC centralizado con rol único global, contexto empresarial (empresa+local), integraciones, APIs, notificaciones, workflows, y convenciones de datos/UI. |
| Principios | Separación por módulos, Core mínimo, dependencias limpias, permisos granulares por aplicativo y acción, contexto empresarial obligatorio en módulos que lo requieran, y calidad de código desde el día 1. |
| Resultado esperado | Instalación flexible por cliente: Core + combinación de módulos según necesidad (Partners/ERP/CRM/RRHH/FMS), con control de acceso, consistencia de datos, integraciones y escalabilidad. |

---

# 1. Objetivo y Criterios de Calidad

Este documento establece la arquitectura base del proyecto Multisoft Suite para que el desarrollo sea mantenible, modular, extensible y escalable. La suite se entrega por cliente instalando únicamente los paquetes requeridos según sus necesidades específicas de negocio.

## 1.1 Criterios de calidad (no negociables)

- Arquitectura modular por paquetes instalables (Composer path repositories).
- Core independiente: no depende de ningún módulo funcional.
- Módulos no se acoplan entre sí: integración mediante Events, Listeners o Contracts definidos en Core.
- Sistema de permisos granular: acceso por aplicativo (módulo) y por acción (operaciones específicas).
- **Rol único global por usuario** en módulos sin contexto (Core, CRM, HR, Reports).
- **Roles múltiples por contexto** en módulos empresariales (ERP, FMS).
- Contexto empresarial obligatorio en módulos que lo requieran (ERP, FMS): Empresa + Local antes de operar.
- PostgreSQL con constraints, índices y optimizaciones desde el inicio.
- Lógica de negocio fuera de Livewire: usar Actions/UseCases/Services.
- Testing obligatorio: tests unitarios para lógica crítica, tests de integración para flujos principales.
- Documentación técnica actualizada: PHPDoc, README por paquete, diagramas ER.
- CI/CD configurado: análisis estático (PHPStan), formateo (Pint), tests automatizados.

---

# 2. Stack Tecnológico

| Componente | Tecnología |
|------------|------------|
| Backend | Laravel 12 |
| UI/Frontend | Livewire 4 + Blade |
| Framework CSS | Bootstrap 5 + Plantilla (Vuexy UI) |
| Base de datos | PostgreSQL (con constraints, índices y optimizaciones) |
| Arquitectura | Monolito modular por paquetes (Core + módulos funcionales) |
| Internacionalización | Idioma en URL (es/en) + middleware de locale |
| Autenticación API | Laravel Sanctum (para futuras integraciones y apps móviles) |
| Jobs/Colas | Laravel Queues + Redis (procesos pesados, reportes, emails masivos) |
| Caché | Redis (permisos, configuraciones, consultas frecuentes) |
| Almacenamiento | Local + S3-compatible (archivos adjuntos, documentos) |
| Observabilidad | Laravel Telescope (desarrollo), logs estructurados por canal |

---

# 3. Arquitectura por Paquetes (Módulos Instalables)

La suite se organiza como un proyecto Laravel (host) que carga paquetes internos ubicados en "modules/*". Cada paquete es instalable vía Composer y registra sus rutas, migraciones, configuración, permisos, events y listeners.

## 3.1 Paquetes y Responsabilidades

| Paquete | Responsabilidad | Depende de |
|---------|-----------------|------------|
| modules/core | Auth, RBAC (roles/permisos), settings, auditoría, adjuntos, notificaciones, eventos base, utilidades comunes. | --- |
| modules/partners | Gestión de terceros (clientes/proveedores/contactos), direcciones, categorización, datos compartidos de negocio. | modules/core |
| modules/erp | Procesos ERP. Incluye Contexto (Empresa+Local), inventarios, compras, ventas, asignación rol-por-contexto. | modules/core + modules/partners |
| modules/fms | FMS: plan de cuentas, asientos contables, libro diario/mayor, estados financieros, integración con ERP. | modules/core + modules/erp |
| modules/hr | RRHH: empleados, contratos, asistencias, planillas, vacaciones, evaluaciones. Puede operar sin contexto ERP. | modules/core |
| modules/crm | CRM: leads, oportunidades, actividades, pipeline de ventas, cotizaciones. Usa partners para clientes potenciales. | modules/core + modules/partners |
| modules/reports | Motor de reportes: generación dinámica, exportación (PDF/Excel), plantillas personalizables, programación. | modules/core |
| modules/integrations | (Futuro) Integraciones externas: SUNAT, bancos, e-commerce, webhooks, APIs de terceros. | modules/core |

## 3.2 Estructura del Repositorio

```
tu-proyecto/
├── app/
├── bootstrap/
├── config/
├── database/
├── modules/                    # Módulos principales
│   ├── Core/
│   │   ├── Config/
│   │   ├── Database/
│   │   │   ├── Migrations/
│   │   │   └── Seeders/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Middleware/
│   │   │   └── Requests/
│   │   ├── Models/
│   │   ├── Providers/
│   │   │   └── CoreServiceProvider.php
│   │   ├── Resources/
│   │   │   ├── views/
│   │   │   └── lang/
│   │   ├── Routes/
│   │   │   ├── web.php
│   │   │   └── api.php
│   │   ├── Services/
│   │   ├── Tests/
│   │   ├── composer.json
│   │   └── module.json
│   │
│   ├── CRM/
│   │   ├── Config/
│   │   ├── Database/
│   │   ├── Http/
│   │   ├── Models/
│   │   ├── Providers/
│   │   │   └── CRMServiceProvider.php
│   │   ├── Resources/
│   │   ├── Routes/
│   │   ├── Services/
│   │   ├── composer.json
│   │   └── module.json
│   │
│   ├── Accounting/
│   └── Inventory/
│
├── packages/                   # Paquetes reutilizables
│   └── vendor-name/
│       └── package-name/
│
├── composer.json
└── README.md
```

## 3.3 Estructura Alternativa con Laravel Modules

Para instalaciones que prefieran usar nwidart/laravel-modules:

```bash
composer require nwidart/laravel-modules
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
```

**Comandos principales:**
```bash
php artisan module:make NombreModulo
php artisan module:make-controller UserController Core
php artisan module:make-model User Core
php artisan module:make-migration create_users_table Core
php artisan module:migrate Core
php artisan module:seed Core
php artisan module:enable Core
php artisan module:disable CRM
php artisan module:list
```

## 3.4 Reglas de Dependencia

- Core NO importa clases de ningún módulo funcional. Es completamente independiente.
- Módulos funcionales (ERP/HR/CRM/FMS/Partners) PUEDEN importar Core.
- Integración entre módulos: usar Events (publicados por un módulo) + Listeners (en otro módulo) o Contracts definidos en Core.
- Si un módulo no está instalado, no deben existir sus rutas, migraciones, ni permisos.
- Los módulos NO deben acoplarse directamente entre sí. Ejemplo: CRM no llama directamente a ERP, sino que escucha eventos de ERP.
- Contracts en Core definen interfaces que los módulos pueden implementar para extensibilidad.

---

# 4. Sistema de Internacionalización (i18n)

El sistema implementa internacionalización completa con idioma expresado como primer segmento de la URL.

## 4.1 Convención de URLs

### Rutas Públicas (Sin autenticación)

| URL | Descripción |
|-----|-------------|
| `/` | Redirige a `/{locale}/` según preferencia del usuario o `es` por defecto |
| `/{locale}/` | Landing page informativa de Multisoft Suite |
| `/{locale}/login` | Inicio de sesión |
| `/{locale}/register` | Registro de usuario |
| `/{locale}/forgot-password` | Solicitar reset de contraseña |
| `/{locale}/reset-password/{token}` | Restablecer contraseña |

### Rutas Protegidas (Requieren autenticación)

| URL | Permiso Requerido | Descripción |
|-----|-------------------|-------------|
| `/{locale}/welcome` | `auth` | **Post-login:** Bienvenida + accesos a módulos permitidos |
| `/{locale}/email/verify` | `auth` | Verificación de email |
| `/{locale}/user/confirm-password` | `auth` | Confirmar contraseña para áreas seguras |
| `/{locale}/two-factor-challenge` | `auth` | Autenticación de dos factores |
| `/{locale}/core/*` | `auth` | Módulo Core (usuarios, roles, configuración) |
| `/{locale}/erp/*` | `auth` + `access.erp` | Módulo ERP |
| `/{locale}/hr/*` | `auth` + `access.hr` | Módulo RRHH |
| `/{locale}/crm/*` | `auth` + `access.crm` | Módulo CRM |
| `/{locale}/fms/*` | `auth` + `access.fms` | Módulo FMS |

### Comportamiento de Protección

- **No autenticado + ruta protegida:** Redirige a `/{locale}/login`
- **Autenticado + ruta no existe:** Muestra página 404 personalizada
- **Autenticado + sin permiso:** Muestra página 403 (Acceso denegado)

## 4.2 Estructura de Archivos de Traducción

```
lang/
├── es/
│   ├── auth.php        # 28 textos de autenticación
│   ├── common.php      # 45 textos comunes (botones, mensajes, estado)
│   └── core.php        # 25 textos del módulo Core
├── en/
│   ├── auth.php        # 28 textos de autenticación
│   ├── common.php      # 45 textos comunes
│   └── core.php        # 25 textos del módulo Core
```

## 4.3 Funciones Helper de Idiomas

```php
// Generar URL con locale específico
multilang_route($name, $locale, $parameters)

// Ruta actual con otro idioma  
current_route_multilang($locale)

// Array de idiomas soportados
supported_locales()

// Nombre display del idioma
locale_name($locale)

// Emoji bandera del idioma
locale_flag($locale)
```

## 4.4 Componente Language Switcher

- **Ubicación:** `<x-language-switcher />`
- **Características:**
  - Dropdown con banderas y nombres
  - Marca idioma actual como activo
  - Mantiene ruta actual al cambiar idioma
  - Responsive (oculta texto en móvil)
  - Integrado en navbar principal

## 4.5 Uso en Blade Templates

```blade
{{ __('auth.login') }}                    <!-- Texto simple -->
{{ __('common.save') }}                   <!-- Botones comunes -->
{{ __('core.users.title') }}              <!-- Textos anidados -->

<a href="{{ multilang_route('users.index', 'es') }}">Usuarios</a>
<a href="{{ current_route_multilang('en') }}">Switch to English</a>

<x-language-switcher />                   <!-- Selector de idioma -->
```

## 4.6 Middleware de Idioma

Se implementa middleware "SetLocale" para establecer el idioma de la aplicación desde el parámetro {locale} en la URL (es/en). El locale se almacena en sesión y se aplica a todas las traducciones de la aplicación.

## 4.7 Protección de Rutas y Redirección Automática

- Toda ruta bajo "/{locale}/..." requiere middleware "auth". Excepto el dominio principal.
- Si el usuario no está logueado e intenta acceder a rutas protegidas, Laravel redirige automáticamente a "/login".
- Si el usuario accede a "/" sin especificar idioma, el sistema redirige a "/es" (idioma por defecto) o al idioma preferido del usuario si está configurado.
- Las rutas de módulos además requieren el permiso "can:access.<modulo>" correspondiente.

## 4.8 Configuración Activa

**Variables de entorno (.env):**
```env
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
```

---

# 5. Seguridad: Roles y Permisos (RBAC Centralizado)

Se adopta RBAC centralizado con **separación clara entre rol global y roles por contexto**.

## 5.1 Arquitectura de Roles - Dos Niveles

```
NIVEL 1: ROL GLOBAL (Core, CRM, HR, Reports)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
users.role_id (FK) → UN SOLO ROL por usuario
Ejemplo:
  - Juan Pérez → Rol: Administrador
  - María López → Rol: Gerente de Ventas
  - Carlos Ruiz → Rol: Usuario Regular

NIVEL 2: ROLES POR CONTEXTO (ERP, FMS)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
erp_user_contexts.role_id (FK) → ROL ESPECÍFICO del contexto
Ejemplo:
  - Juan en Multilens/Lima → Rol: Administrador
  - Juan en Medilens/Iquitos → Rol: Gerente
  - María en Multilens/Lima → Rol: Contador
```

## 5.2 Ventajas de Rol Único Global

| Aspecto | Beneficio |
|---------|-----------|
| **Claridad** | Un usuario = un rol. No hay ambigüedad. |
| **Performance** | Sin JOINs con pivot en queries globales. |
| **Seguridad** | Permisos claros y predecibles. |
| **Mantenibilidad** | Código más simple, menos casos edge. |
| **UX** | Administradores entienden fácilmente. |
| **Escalabilidad** | Contextos ERP siguen usando múltiples roles sin conflicto. |

## 5.3 Implementación del Modelo User

**Relación con rol:**
```php
// Relación BelongsTo (rol único)
public function role(): BelongsTo
{
    return $this->belongsTo(Role::class, 'role_id');
}

// Verificar rol
public function hasRole(string $roleName): bool
{
    return $this->role?->name === $roleName;
}
```

**Manejo de permisos:**
```php
// ANTES (múltiples roles)
$user->roles->contains('name', 'admin');

// DESPUÉS (rol único)
$user->role?->name === 'admin';
// o
$user->hasRole('admin');
```

**Asignar rol:**
```php
// ANTES
$user->roles()->attach($roleId);

// DESPUÉS
$user->update(['role_id' => $roleId]);
```

## 5.4 Dos Niveles de Permiso

- **Permiso de acceso al aplicativo** (1 por módulo): "access.erp", "access.hr", "access.crm", "access.partners", "access.fms".
- **Permisos por acción** (granulares): "erp.invoices.view", "erp.invoices.create", "erp.invoices.post", "hr.employees.edit", "fms.entries.approve".

## 5.5 Convención de Nombres de Permisos

**Formato:** `<modulo>.<recurso>.<accion>`

- Acceso al módulo: "access.<modulo>"
- Ver: "view" | Crear: "create" | Editar: "edit" | Eliminar: "delete"
- Acciones de negocio: "post", "approve", "cancel", "export", "import", "close", "reopen"

## 5.6 Implementación en Rutas y UI

- **Enrutamiento:** el grupo del módulo exige "can:access.<modulo>".
- **Pantallas/acciones:** añade "can:<permiso>" según corresponda usando middleware o gates.
- **Menú dinámico:** mostrar módulos y opciones con "@can()" para evitar enlaces no autorizados.
- **Livewire:** validar permisos en mount() y en cada método público que realice acciones sensibles.
- **API:** aplicar permisos mediante policies y gates en cada endpoint.

## 5.7 Seguridad Adicional

- **CSRF Protection:** habilitado por defecto en Laravel para todos los formularios.
- **Rate Limiting:** limitar intentos de login (máx. 5 intentos por minuto), APIs (100 requests/minuto por usuario).
- **2FA:** opcional para usuarios administradores y roles críticos.
- **Logs de accesos fallidos:** registrar intentos de login fallidos para detección de ataques.
- **Encriptación de campos sensibles:** salarios, datos bancarios, información médica (usar Laravel encrypted casts).
- **Auditoría de cambios:** registrar quién, cuándo y qué cambió en entidades críticas.
- **Sesiones seguras:** usar driver "database" o "redis" con timeout apropiado (120 minutos por defecto).

---

# 6. Contexto Empresarial (Empresa + Local) y Rol por Contexto

Los módulos que requieren contexto empresarial (ERP, FMS) exigen que el usuario elija un contexto de trabajo antes de operar.

## 6.1 Modelo de Datos

**Tablas mínimas recomendadas dentro de módulos con contexto:**

- `erp_group_companies`: empresas general (en monoempresa puede existir una empresa por defecto) para un cliente.
- `erp_companies`: empresas operativas puede existir una o mas empresa (Multilens, Medilens).
- `erp_locations`: locales/sedes por empresa (LIMA, HUANCAYO, AREQUIPA, etc.).
- `erp_user_contexts`: asignación usuario + empresa + local + rol (rol efectivo dentro del contexto).

**Campos clave de `erp_user_contexts`:**
```
user_id, company_id, location_id, role_id, is_default, status, created_at, updated_at
```

## 6.2 Flujo de Ingreso a Módulos con Contexto

1. Usuario entra a "/{locale}/erp" o "/{locale}/fms".
2. Middleware valida: "auth" → "can:access.erp" → "EnsureErpContextSelected".
3. Si no hay contexto en sesión, redirige a pantalla Livewire "Seleccionar Contexto".
4. El usuario elige (Empresa + Local). El sistema valida que exista asignación en "erp_user_contexts".
5. Se guarda el contexto en sesión (company_id, location_id, role_id).
6. El usuario continúa a las pantallas del módulo con el contexto activo.
7. El contexto se mantiene en sesión hasta que el usuario lo cambie explícitamente o cierre sesión.

## 6.3 Permisos por Contexto (Rol Efectivo)

El rol y permisos efectivos dentro de módulos con contexto se determinan por el contexto seleccionado:

- **Acceso al módulo:** "access.erp" (global, evaluado antes de seleccionar contexto).
- **Acciones dentro del módulo:** dependen del rol del contexto seleccionado.
- **Ejemplo:** Joel puede ser "Gerente de Ventas" en Multilens/LIMA y "Almacenero" en Multilens/HUANCAYO.
- **Cambio de contexto:** al cambiar de contexto, cambian automáticamente los permisos efectivos del usuario.

## 6.4 Filtro de Datos por Contexto (Global Scopes)

Todos los modelos de módulos con contexto deben aplicar filtrado automático:

- Tablas deben incluir "company_id" y, cuando aplique, "location_id".
- Los modelos aplican un Global Scope para filtrar siempre por el contexto activo.
- Esto evita fugas de datos entre empresas y locales.

---

# 7. Datos Compartidos: Partners (Terceros)

El paquete Partners concentra los terceros compartidos por múltiples módulos.

## 7.1 Tablas Mínimas Sugeridas

- `partners`: entidad principal (persona/empresa), tipo_documento, documento, nombre, razón_social, estado.
- `partner_addresses`: direcciones físicas y de entrega.
- `partner_contacts`: teléfonos, correos, contactos específicos.
- `partner_roles`: roles del partner (cliente, proveedor, empleado, lead).
- `partner_tags`: etiquetas y categorización flexible.
- `partner_relationships`: relaciones entre partners (empresa-contacto, matriz-sucursal).

## 7.2 Integración con Otros Módulos

Partners es consumido por múltiples módulos sin acoplamiento directo:

- **ERP:** usa partners como clientes y proveedores en compras/ventas.
- **CRM:** usa partners como leads y oportunidades de negocio.
- **RRHH:** puede referenciar partners como empleados.
- **FMS:** usa partners para cuentas por cobrar/pagar.
- **Integración mediante eventos:** cuando se crea/modifica un partner, se disparan eventos que otros módulos pueden escuchar.

---

# 8. Sistema de Notificaciones

## 8.1 Canales de Notificación

- **In-app (base de datos):** notificaciones dentro de la aplicación, badge de contador, panel de notificaciones.
- **Email:** para notificaciones importantes que requieren acción fuera de la aplicación.
- **Broadcast (WebSockets - futuro):** notificaciones en tiempo real usando Laravel Broadcasting + Pusher/Socket.io.
- **SMS (futuro):** para alertas críticas (opcional, integración con proveedor de SMS).

## 8.2 Tipos de Notificaciones por Módulo

- **ERP:** aprobación de cotizaciones, alertas de stock bajo, vencimiento de facturas.
- **RRHH:** aprobación de vacaciones, cumpleaños de empleados, vencimiento de contratos.
- **CRM:** nuevos leads asignados, tareas pendientes, oportunidades por cerrar.
- **FMS:** asientos pendientes de aprobación, cierre de período contable.
- **Core:** cambios de contraseña, sesiones sospechosas, recordatorios configurables.

## 8.3 Implementación

Usar Laravel Notifications con canales configurables por tipo de notificación. Los módulos definen sus propias Notification classes y las envían mediante eventos de negocio.

---

# 9. Workflows y Sistema de Aprobaciones

## 9.1 Estados de Documentos

Estados estándar para entidades que requieren aprobación:

| Estado | Descripción |
|--------|-------------|
| DRAFT | Borrador: documento en edición, no genera impacto |
| PENDING | Pendiente: enviado para aprobación, esperando revisión |
| APPROVED | Aprobado: aprobado por autoridad competente, puede generar impacto |
| REJECTED | Rechazado: no aprobado, requiere correcciones o es descartado |
| CANCELLED | Anulado: documento que estaba aprobado pero fue anulado posteriormente |
| CLOSED | Cerrado: documento completado, no admite más modificaciones |

## 9.2 Niveles de Aprobación

Soportar aprobaciones multinivel configurables:

- **Nivel 1:** Supervisor inmediato (ej. jefe de área aprueba vacaciones).
- **Nivel 2:** Gerencia (ej. gerente aprueba compras mayores a X monto).
- **Nivel 3:** Dirección/Finanzas (ej. CFO aprueba gastos extraordinarios).
- **Configuración flexible:** definir niveles y montos por tipo de documento y empresa.

## 9.3 Historial de Cambios de Estado

Tabla de auditoría de workflows:

```
workflow_id, entity_type, entity_id, previous_status, new_status
approved_by (user_id), approved_at, comments
ip_address, user_agent (para auditoría completa)
```

## 9.4 Notificaciones de Workflow

Integrar con sistema de notificaciones: notificar automáticamente a aprobadores cuando un documento requiere su atención.

---

# 10. Gestión de Archivos y Adjuntos

## 10.1 Estrategia de Almacenamiento

- **Storage primario:** Local (desarrollo/staging) + S3-compatible (producción: AWS S3, DigitalOcean Spaces, MinIO).
- **Estructura de carpetas:** organizar por módulo/entidad/id (ej. erp/invoices/123/documento.pdf).
- **Límites:** tamaño máximo por archivo (10MB por defecto), tipos permitidos configurables.
- **Versionamiento:** mantener versiones anteriores de documentos críticos.

## 10.2 Tabla de Adjuntos (Core)

Tabla polimórfica en Core para adjuntos:

```
attachments: id, attachable_type, attachable_id, filename, filepath, mime_type, size, uploaded_by, created_at
```

## 10.3 Tipos de Archivos Permitidos

- **Documentos:** PDF, DOCX, XLSX, TXT
- **Imágenes:** JPG, PNG, GIF (para productos, perfiles)
- **Comprimidos:** ZIP (para respaldos, exportaciones masivas)
- **Configuración por módulo:** cada módulo puede restringir tipos según contexto

---

# 11. Jobs y Colas (Laravel Queues)

## 11.1 Casos de Uso por Módulo

- **ERP:** generación de reportes de ventas mensuales, cálculo de inventario valorizado, sincronización con sistemas externos.
- **RRHH:** procesamiento de planillas (nómina), cálculo de CTS, gratificaciones, generación masiva de recibos.
- **CRM:** envío masivo de emails de marketing, actualización de pipeline, limpieza de leads antiguos.
- **FMS:** cierre de mes contable, generación de estados financieros consolidados.
- **Core:** envío de notificaciones por email, limpieza de logs antiguos, generación de backups.

## 11.2 Configuración de Colas

- **Driver:** Redis (producción) / Database (desarrollo)
- **Colas múltiples:** "default", "high-priority", "reports", "emails", "notifications"
- **Workers:** configurar número de workers según carga (supervisor/systemd)
- **Retry strategy:** reintentos automáticos con backoff exponencial para jobs fallidos
- **Failed jobs:** tabla de jobs fallidos para diagnóstico y reintentos manuales

---

# 12. Estrategia de Caché

## 12.1 Qué Cachear

- **Permisos por usuario/rol:** evitar consultas repetidas a la base de datos en cada request.
- **Configuraciones del sistema:** settings que no cambian frecuentemente.
- **Listas de valores (catálogos):** monedas, tipos de documento, países.
- **Consultas frecuentes por contexto:** productos activos, clientes frecuentes.
- **Resultados de reportes:** cachear reportes pesados con TTL apropiado.

## 12.2 Estrategia de Invalidación

- **Tag-based caching:** usar tags de Redis para invalidar grupos de caché (ej. "users:123:permissions").
- **Invalidación por eventos:** cuando cambia un permiso o rol, invalidar caché de permisos.
- **Invalidación por contexto:** al cambiar de empresa/local, invalidar caché específico del contexto anterior.
- **TTL apropiado:** configurar tiempo de vida según frecuencia de cambio (permisos: 1 hora, catálogos: 24 horas).

## 12.3 Driver de Caché

Redis en producción (soporta tags, concurrencia, persistencia), File/Array en desarrollo.

---

# 13. API REST (Futuro Escalamiento)

## 13.1 Estructura de API

- **Versionado:** /api/v1/ (primera versión), mantener compatibilidad hacia atrás.
- **Autenticación:** Laravel Sanctum para tokens de API (stateless).
- **Rate limiting:** limitar requests por usuario/IP (100/minuto usuarios autenticados, 10/minuto anónimos).
- **Responses estandarizados:** JSON con estructura consistente { success, data, message, errors }.

## 13.2 Endpoints por Módulo

- **Core:** /api/v1/auth/login, /api/v1/auth/logout, /api/v1/users
- **Partners:** /api/v1/partners, /api/v1/partners/{id}
- **ERP:** /api/v1/erp/invoices, /api/v1/erp/products
- **CRM:** /api/v1/crm/leads, /api/v1/crm/opportunities
- **Documentación:** generar automáticamente con Scramble/L5-Swagger (OpenAPI 3.0)

## 13.3 Seguridad API

- **Tokens con expiración:** tokens de Sanctum con TTL configurable.
- **Scopes/abilities:** definir permisos específicos para tokens de API.
- **Validación estricta:** validar todos los inputs con Form Requests.
- **Throttling agresivo:** prevenir abuso de API con rate limiting por endpoint crítico.

---

# 14. Sistema de Menús Dinámicos por Módulo

La suite implementa un sistema de navegación donde cada módulo tiene su propio menú lateral.

## 14.1 Arquitectura de Navegación

| Nivel | Ubicación | Ejemplo |
|-------|-----------|---------|
| 1. Módulos | Module Switcher (navbar) | Core, ERP, HR, CRM |
| 2. Categorías | Sidebar (colapsable) | Inventario, Ventas, Compras |
| 3. Items | Submenú expandido | Artículos, Kardex, Facturas |

## 14.2 Componentes del Sistema

- **MenuService:** Servicio singleton para detectar módulo activo, cargar menús y filtrar por permisos.
- **DetectActiveModule:** Middleware que detecta el módulo basándose en la URL y comparte datos con las vistas.
- **sidebar-menu.blade.php:** Componente que renderiza menú dinámico con items simples, colapsables y headers.
- **module-switcher.blade.php:** Dropdown en navbar para cambiar entre módulos.

## 14.3 Configuración de Menús

Cada módulo define su menú en: `modules/{modulo}/src/config/menu.php`

```php
return [
    'module' => [
        'name' => 'erp',
        'display_name' => ['es' => 'ERP', 'en' => 'ERP'],
        'icon' => 'ti tabler-building',
        'color' => 'info',
    ],
    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'erp.dashboard',
            'permission' => 'access.erp',
        ],
        [
            'key' => 'inventory',
            'title' => ['es' => 'Inventario', 'en' => 'Inventory'],
            'icon' => 'ti tabler-package',
            'permission' => 'erp.inventory.view',
            'children' => [
                ['key' => 'items', 'title' => [...], 'route' => 'erp.inventory.items.index'],
                ['key' => 'categories', 'title' => [...], 'route' => '...'],
            ],
        ],
    ],
];
```

## 14.4 Equivalencia de URLs y Navegación

| URL | Auth | Módulo | Resultado |
|-----|------|--------|----------|
| `/` | No | - | Redirige a `/{locale}/` |
| `/{locale}/` | No | - | Landing page pública |
| `/{locale}/login` | No | - | Formulario de login |
| `/{locale}/welcome` | **Sí** | Core | Bienvenida post-login + cards de módulos |
| `/{locale}/core/` | Sí | Core | Dashboard Core con menú Core |
| `/{locale}/erp/` | Sí | ERP | Dashboard ERP con menú ERP |
| `/{locale}/erp/inventory/items` | Sí | ERP | Menú ERP con Inventario expandido |
| `/{locale}/hr/` | Sí | HR | Dashboard HR con menú HR |
| `/{locale}/crm/` | Sí | CRM | Dashboard CRM con menú CRM |
| `/{locale}/fms/` | Sí | FMS | Dashboard FMS con menú FMS |
| Ruta no encontrada | Sí | - | Página 404 personalizada |
| Sin permiso al módulo | Sí | - | Página 403 (Acceso denegado) |

### Flujo Post-Login

```
Usuario hace login exitoso
        ↓
Redirige a /{locale}/welcome
        ↓
Muestra bienvenida + cards con módulos permitidos
        ↓
Usuario selecciona módulo → /{locale}/{modulo}/
```

## 14.5 Permisos y Visibilidad

- El Module Switcher solo muestra módulos a los que el usuario tiene acceso (`access.<modulo>`).
- Los items del menú se filtran según los permisos del usuario.
- Los headers se ocultan automáticamente si no hay items visibles después de ellos.

---

# 15. Multimoneda y Localización Avanzada

## 15.1 Gestión de Monedas

- **Tabla de monedas:** currencies (code, name, symbol, decimals, is_active).
- **Tabla de tipos de cambio:** exchange_rates (from_currency, to_currency, rate, valid_from, valid_to).
- **Actualización automática:** job scheduled para actualizar tipos de cambio diarios desde API externa.
- **Moneda base por empresa:** cada empresa define su moneda base para reportes.

## 15.2 Conversión en Reportes

Los reportes financieros deben poder mostrar valores en moneda base o en moneda específica, con conversión automática usando los tipos de cambio vigentes.

## 15.3 Formatos Regionales

- **Fechas:** formato configurable por usuario/empresa (DD/MM/YYYY vs MM/DD/YYYY).
- **Números:** separadores de miles y decimales según región (1.000,50 vs 1,000.50).
- **Moneda:** mostrar símbolo correcto según moneda (S/ 100.00, $ 100.00, € 100.00).

---

# 16. Convenciones PostgreSQL (Robustez desde el día 1)

## 16.1 Nombres de Tablas

Nombres con prefijo por paquete para evitar colisiones:

| Paquete | Prefijo | Ejemplos |
|---------|---------|----------|
| Core | core_* | core_users, core_roles, core_permissions, core_audit_logs |
| Partners | partners_* | partners, partner_addresses, partner_contacts |
| ERP | erp_* | erp_companies, erp_locations, erp_invoices, erp_products |
| FMS | fms_* | fms_accounts, fms_entries |
| HR | hr_* | hr_employees, hr_contracts, hr_attendances |
| CRM | crm_* | crm_leads, crm_opportunities, crm_activities |

## 16.2 Constraints y Validaciones

- **Foreign Keys:** siempre definir con acciones ON DELETE (CASCADE/RESTRICT según lógica).
- **Unique Constraints:** para campos que deben ser únicos (documento, email, código).
- **Check Constraints:** validaciones de negocio (ej. amount >= 0, status IN (...)).
- **Not Null:** definir NOT NULL donde el campo sea obligatorio.

## 16.3 Índices Críticos

- **Documentos:** índice en (tipo_documento, numero_documento).
- **Búsquedas de texto:** índice en nombre, razón_social usando GIN/trigram para LIKE.
- **Contexto ERP:** índice compuesto en (company_id, location_id) para filtrado rápido.
- **Fechas:** índice en created_at, issue_date para reportes por período.
- **Estados:** índice en status para filtros frecuentes.

## 16.4 SoftDeletes y Auditoría

- **Usar solo en entidades donde sea indispensable:** usuarios, partners, productos.
- **NO usar en tablas transaccionales:** facturas, asientos (usar campo "status" o "cancelled_at").
- **Auditoría completa:** registrar todas las operaciones críticas en "core_audit_logs".
- **Campos de auditoría estándar:** created_by, updated_by, deleted_by (user_id).

## 16.5 Performance y Optimización

- **Evitar N+1 queries:** usar eager loading (with()) en consultas Eloquent.
- **Particionamiento:** considerar particionar tablas muy grandes (auditoría, logs) por fecha.
- **Vacuum y Analyze:** configurar autovacuum en PostgreSQL para mantener rendimiento.
- **Query monitoring:** usar Laravel Telescope en desarrollo para detectar queries lentas.

---

# 17. Livewire 4 + Bootstrap 5: Patrón UI Consistente

## 17.1 Separación de Responsabilidades

Livewire maneja SOLO interacción y estado de UI. La lógica de negocio SIEMPRE va en Actions/UseCases/Services.

- **Livewire Component:** validación de inputs, estado de UI (loading, modals), llamadas a Actions.
- **Actions/UseCases:** lógica de negocio, validaciones complejas, transacciones de base de datos.
- **Models:** solo relaciones y accessors/mutators simples.

## 17.2 Patrón de Pantallas Estándar

- **Index:** tabla con listado, filtros, paginación, acciones masivas.
- **Form (Create/Edit):** formulario con validación en tiempo real, guardado.
- **Show:** detalle de entidad con información completa, acciones disponibles.
- **Modals:** para acciones rápidas (confirmaciones, formularios simples).

## 17.3 Componentes Reutilizables

- **Form inputs:** input-text, input-select, input-date con validación integrada.
- **Tablas:** table-wrapper con paginación, ordenamiento, filtros.
- **Modals:** modal-confirm, modal-form para acciones comunes.
- **Alerts:** alert-success, alert-error para feedback al usuario.
- **Badges:** para estados (pendiente, aprobado, cancelado) con colores consistentes.

## 17.4 Menú y Navegación

Menú dinámico basado en permisos:

- **Sidebar:** módulos principales (ERP, CRM, RRHH, etc.) con íconos.
- **Breadcrumbs:** navegación jerárquica para contexto del usuario.
- **Selector de contexto:** componente en navbar para cambiar empresa/local (ERP/FMS).
- **Notificaciones:** badge con contador y dropdown de notificaciones recientes.

---

# 18. Testing y Calidad de Código

## 18.1 Tipos de Tests Obligatorios

- **Unit Tests:** para Actions/UseCases, validar lógica de negocio aislada.
- **Feature Tests:** para flujos completos (crear factura, aprobar vacaciones, cerrar oportunidad).
- **Browser Tests (Dusk - opcional):** para flujos críticos de UI que requieren JavaScript.
- **API Tests:** para endpoints de API, validar responses y códigos de estado.

## 18.2 Cobertura de Tests

Objetivo mínimo: 70% de cobertura en Actions/UseCases críticos. Priorizar flujos de dinero (facturación, pagos, nómina) y aprobaciones.

## 18.3 Herramientas de Calidad

- **PHPStan (nivel 6+):** análisis estático, detectar errores antes de runtime.
- **Laravel Pint:** formateo automático de código según estándar Laravel.
- **Pest/PHPUnit:** framework de testing, preferir Pest por sintaxis clara.
- **PHP Insights:** métricas de calidad de código (complejidad, duplicación).
- **IDE Helper:** generar autocomplete para Facades, Models en IDE.

## 18.4 CI/CD Pipeline

Pipeline automatizado en GitHub Actions / GitLab CI:

1. composer install + npm install
2. PHPStan análisis estático
3. Laravel Pint verificación de formato
4. Pest/PHPUnit ejecución de tests
5. Deploy automático a staging si todo pasa
6. Deploy manual a producción con aprobación

---

# 19. Backup y Recuperación

## 19.1 Estrategia de Backups

- **PostgreSQL:** dumps automáticos diarios completos + incrementales cada 6 horas.
- **Archivos adjuntos:** sincronización diaria a bucket de respaldo en S3.
- **Retención:** 7 días completos, 4 semanas incrementales, 12 meses mensuales.
- **Almacenamiento:** backups en ubicación geográfica diferente al servidor principal.
- **Encriptación:** backups encriptados con GPG antes de subir a almacenamiento externo.

## 19.2 Procedimiento de Restauración

- Documentar pasos exactos para restaurar desde backup.
- Probar restauración trimestralmente en ambiente de staging.
- **Tiempo de recuperación objetivo (RTO):** < 4 horas.
- **Punto de recuperación objetivo (RPO):** < 6 horas.

## 19.3 Backups por Contexto (Opcional)

Para clientes que requieran backups independientes por empresa, implementar exportación/importación por company_id con restauración selectiva.

---

# 20. Migraciones, Seeders y Permisos por Paquete

## 20.1 Migraciones por Paquete

Cada paquete carga sus propias migraciones usando loadMigrationsFrom() en el ServiceProvider.

- Nombrar migraciones descriptivamente: 2026_01_31_create_erp_invoices_table.php
- Orden de ejecución: considerar dependencias entre tablas (foreign keys).
- Rollback seguro: todas las migraciones deben tener método down() funcional.
- Migraciones de datos: separar migraciones de estructura de migraciones de datos/seeders.

## 20.2 Seeders por Módulo

Cada módulo provee seeders para datos iniciales:

- **Permissions Seeder:** registrar todos los permisos del módulo (incluye "access.<modulo>").
- **Roles Seeder:** crear roles iniciales con permisos asignados (Admin, Usuario).
- **Demo Data Seeder:** datos de ejemplo para desarrollo/demo (opcional).
- **Ejecutar seeders en orden:** Core → Partners → ERP/HR/CRM/FMS.

## 20.3 Registro de Permisos

Convención para registrar permisos al instalar módulo:

- Cada módulo define array de permisos en su PermissionsSeeder.
- Formato: ["access.erp", "erp.invoices.view", "erp.invoices.create", ...]
- Registrar en tabla core_permissions con guard_name "web".
- Asignar permisos de acceso al rol Admin por defecto.

---

# 21. Observabilidad y Auditoría

## 21.1 Auditoría en Core

Registrar acciones críticas en tabla core_audit_logs:

- Operaciones CRUD en entidades críticas: facturas, asientos, contratos.
- Cambios de estado: aprobar, anular, cerrar documentos.
- Cambios de permisos: asignación/revocación de roles.
- Accesos fallidos: intentos de acceso no autorizados.
- Exportaciones masivas: quién exportó qué datos y cuándo.

## 21.2 Estructura de Audit Logs

```
id, user_id, action, auditable_type, auditable_id, 
old_values (JSON), new_values (JSON), 
ip_address, user_agent, context (company_id, location_id), created_at
```

## 21.3 Logs por Canal

Configurar canales de log separados para cada módulo:

- **stack (default):** logs generales de aplicación
- **erp:** logs específicos de operaciones ERP
- **fms:** logs de FMS (críticos)
- **hr:** logs de RRHH (datos sensibles)
- **security:** logs de seguridad (login, permisos)

## 21.4 Manejo de Errores

Excepciones tipadas por módulo para diagnósticos claros:

- **Business exceptions:** InsufficientStockException, InvoiceAlreadyPostedException
- **Validation exceptions:** InvalidDocumentNumberException
- **Authorization exceptions:** UnauthorizedContextAccessException
- **Reportar excepciones a servicio externo:** Sentry, Bugsnag (producción)

---

# 22. Estrategia de Despliegue y Ambientes

## 22.1 Ambientes

- **Local:** desarrollo individual, base de datos local, debugging habilitado.
- **Staging:** réplica de producción, para pruebas antes de despliegue.
- **Production:** ambiente de producción, optimizado, sin debugging.

## 22.2 Variables de Entorno Críticas

```env
APP_ENV, APP_KEY, APP_DEBUG
DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
REDIS_HOST, REDIS_PASSWORD (caché y queues)
AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION (S3)
MAIL_MAILER, MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD (notificaciones)
ERP_DEFAULT_CURRENCY, FMS_FISCAL_YEAR_START (por módulo)
```

## 22.3 Proceso de Actualización

Despliegue sin downtime:

1. git pull + composer install --no-dev
2. php artisan migrate --force (migraciones)
3. php artisan config:cache + route:cache + view:cache
4. php artisan queue:restart (reiniciar workers)
5. Verificar healthcheck endpoint
6. Rollback automático si healthcheck falla

## 22.4 Rollback Strategy

Plan de rollback en caso de fallas:

- **Git:** mantener último commit estable tagged (v1.0.0, v1.1.0).
- **Database:** backup inmediato antes de cada migración en producción.
- **Rollback rápido:** git checkout <tag> + composer install + migrate:rollback.
- **Comunicación:** notificar a usuarios si rollback afecta funcionalidad.

---

# 23. Entregas por Cliente (Instalación Modular)

Ejemplos de combinaciones de paquetes según requerimientos del cliente:

| Cliente / Necesidad | Paquetes Instalados |
|---------------------|---------------------|
| Solo RRHH | Core + HR |
| Solo ERP | Core + Partners + ERP |
| Solo CRM | Core + Partners + CRM |
| ERP + FMS | Core + Partners + ERP + FMS |
| ERP + RRHH | Core + Partners + ERP + HR |
| Suite Completa | Core + Partners + ERP + FMS + HR + CRM + Reports |

Cada cliente paga solo por los módulos que instala, permitiendo escalamiento gradual según crecimiento del negocio.

---

# 24. Checklist de Arranque del Proyecto

**Para que el proyecto nazca sano y con bases sólidas:**

- [ ] 1. Crear estructura de paquetes en modules/ (core, partners, erp, fms, hr, crm, reports).
- [ ] 2. Configurar composer.json para path repositories y autoload de paquetes.
- [ ] 3. Implementar Core: Auth, RBAC (roles/permisos con rol único global), auditoría, notificaciones, adjuntos.
- [ ] 4. Definir permisos iniciales: access.<modulo> + permisos de acción por recurso en cada módulo.
- [ ] 5. Implementar middleware locale (es/en) y proteger rutas con auth + permisos.
- [ ] 6. Implementar sistema de internacionalización con helpers y componente language switcher.
- [ ] 7. Crear sistema de contexto empresarial (Empresa + Local) con tabla erp_user_contexts.
- [ ] 8. Implementar selector de contexto como componente Livewire reutilizable.
- [ ] 9. Aplicar Global Scopes por contexto en todos los modelos ERP/FMS.
- [ ] 10. Establecer plantilla UI Bootstrap 5 y patrón Livewire (Index/Form/Show/Modal).
- [ ] 11. Crear componentes reutilizables: form-input, table-wrapper, modal-confirm, alerts.
- [ ] 12. Configurar base de datos PostgreSQL con constraints, índices y optimizaciones.
- [ ] 13. Implementar sistema de notificaciones (in-app + email).
- [ ] 14. Configurar Laravel Queues con Redis para jobs pesados.
- [ ] 15. Configurar estrategia de caché con Redis y tags.
- [ ] 16. Implementar workflows de aprobación con estados y auditoría.
- [ ] 17. Crear seeders de roles/permisos iniciales y usuario administrador.
- [ ] 18. Configurar PHPStan, Pint, Pest para calidad de código.
- [ ] 19. Configurar CI/CD pipeline en GitHub Actions / GitLab CI.
- [ ] 20. Implementar sistema de backups automáticos (base de datos + archivos).
- [ ] 21. Documentar procedimientos: instalación, despliegue, rollback, recuperación.
- [ ] 22. Crear tests básicos para flujos críticos de cada módulo.
- [ ] 23. Preparar estructura de API REST (futuro) con Sanctum.
- [ ] 24. Configurar ambientes: local, staging, production con variables apropiadas.
- [ ] 25. Crear README.md general del proyecto y por cada paquete.
- [ ] 26. Generar diagramas ER para cada módulo (mínimo Core, ERP, FMS).
- [ ] 27. Implementar sistema de menús dinámicos por módulo.

---

# Notas Finales

**Este es un documento de referencia vivo que se actualizará según la evolución del proyecto Multisoft Suite.**

- La arquitectura descrita es flexible y permite agregar nuevos módulos sin afectar los existentes.
- Se prioriza la calidad sobre la velocidad: mejor construir bien desde el inicio que refactorizar después.
- La documentación técnica debe mantenerse actualizada en paralelo con el código.
- Los tests no son opcionales: protegen la inversión y dan confianza para refactorizar.
- La seguridad es responsabilidad de todos: validar inputs, proteger rutas, auditar cambios.
- El rendimiento importa: cachear inteligentemente, optimizar queries, usar jobs para procesos pesados.
- La suite debe ser agradable de usar: UI consistente, flujos intuitivos, feedback claro al usuario.

---

*--- Documento de Arquitectura Multisoft Suite v2.0 ---*

*Unificación de documentos: Arquitectura base + Sistema de Idiomas + Rol Único Global + Estructura Modular*

*Mantener actualizado conforme evoluciona el proyecto*
