# Documento de Requisitos

## Introducción

Este documento especifica los requisitos para refactorizar el módulo HR (Recursos Humanos) del sistema Multisoft Suite, específicamente la gestión de empleados. La refactorización corrige la arquitectura de datos para alinearla con la jerarquía multi-tenant del sistema (Tenant → Group Company → Company → Location), eliminando la relación incorrecta many-to-many entre empleados y grupos, y estableciendo que cada empleado pertenece a una empresa y un local específicos.

## Glosario

- **Tenant**: Cliente que contrata el sistema SaaS
- **Group_Company**: Operación por país (PE, EC, CO) dentro de un tenant
- **Company**: Empresa que factura (Multilens, Medilens) dentro de un grupo
- **Location**: Local o sede física (Lima, Huancayo, Arequipa) dentro de una empresa
- **Empleado**: Usuario del sistema que pertenece a una empresa y local específicos
- **Módulo_HR**: Módulo de Recursos Humanos del sistema
- **Contexto**: Información de tenant y grupo obtenida de la sesión/URL actual
- **Componente_Livewire**: Componente de interfaz usando Livewire 4
- **Eliminación_Lógica**: Eliminación que marca registros como eliminados sin borrarlos físicamente

## Requisitos

### Requisito 1: Estructura de Datos Correcta

**Historia de Usuario:** Como arquitecto del sistema, quiero que los empleados pertenezcan a una empresa y local específicos (local opcional), para que la jerarquía de datos sea consistente con la arquitectura multi-tenant.

#### Criterios de Aceptación

1. EL Módulo_HR DEBERÁ almacenar cada Empleado con tenant_id, group_company_id y company_id
2. CUANDO se crea un Empleado, EL Sistema DEBERÁ asignar tenant_id y group_company_id automáticamente desde el Contexto
3. EL Sistema DEBERÁ requerir que company_id sea proporcionado por el usuario
4. EL Sistema DEBERÁ permitir que location_id sea opcional (puede ser NULL)
5. EL Sistema DEBERÁ permitir que user_id sea opcional (puede ser NULL) para empleados sin acceso al sistema
6. EL Sistema NO DEBERÁ almacenar password en hr_empleados (solo en users si el empleado tiene acceso)
7. EL Sistema DEBERÁ almacenar campos básicos de RRHH: documento_tipo, documento_numero, telefono, codigo_empleado, cargo, fecha_ingreso
8. EL Sistema DEBERÁ eliminar la tabla hr_empleado_group_access y todo el código relacionado
9. EL Sistema DEBERÁ aplicar restricciones de clave foránea para tenant_id, group_company_id, company_id, location_id y user_id (cuando estén presentes)

### Requisito 2: Filtrado Automático por Grupo

**Historia de Usuario:** Como usuario navegando en `/es/PE/hr/empleados`, quiero ver solo empleados de empresas del grupo PE, para trabajar únicamente con datos relevantes a mi contexto.

#### Criterios de Aceptación

1. CUANDO un usuario accede a la lista de empleados, EL Sistema DEBERÁ filtrar Empleados por el group_company_id del Contexto
2. CUANDO la URL cambia a un grupo diferente, EL Sistema DEBERÁ actualizar automáticamente los Empleados mostrados
3. EL Sistema DEBERÁ mostrar solo Empleados cuya empresa pertenezca al grupo actual
4. EL Sistema NO DEBERÁ requerir selección manual de grupo en ninguna interfaz

### Requisito 3: Selector de Empresa Filtrado

**Historia de Usuario:** Como usuario creando un empleado, quiero seleccionar solo empresas del grupo actual, para mantener la consistencia de datos.

#### Criterios de Aceptación

1. CUANDO se muestra el formulario de empleado, EL Sistema DEBERÁ mostrar solo Companies que pertenezcan al group_company_id actual
2. EL Sistema DEBERÁ mostrar el selector de empresa como un dropdown con nombres de empresas
3. CUANDO no existan empresas para el grupo actual, EL Sistema DEBERÁ mostrar un mensaje apropiado
4. EL Sistema DEBERÁ validar que el company_id seleccionado pertenezca al grupo actual antes de guardar

### Requisito 4: Selector de Local Dependiente

**Historia de Usuario:** Como usuario creando un empleado, quiero seleccionar solo locales de la empresa seleccionada, para asegurar que el empleado esté asignado correctamente.

#### Criterios de Aceptación

1. CUANDO se selecciona una empresa, EL Sistema DEBERÁ actualizar el selector de local para mostrar solo Locations pertenecientes a esa empresa
2. EL Sistema DEBERÁ permitir que el selector de local permanezca vacío (location_id puede ser NULL)
3. CUANDO la empresa seleccionada cambia, EL Sistema DEBERÁ limpiar el local previamente seleccionado
4. SI se proporciona un location_id, EL Sistema DEBERÁ validar que pertenezca al company_id seleccionado antes de guardar

### Requisito 5: Visualización de Información en Listado

**Historia de Usuario:** Como usuario viendo la lista de empleados, quiero ver información relevante de cada empleado, para identificar rápidamente su ubicación y datos básicos.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ mostrar código de empleado, nombre, documento, cargo, empresa y local en la vista de lista
2. EL Sistema DEBERÁ mostrar el estado del empleado visualmente (activo/suspendido/cesado)
3. CUANDO se muestren relaciones (empresa, local, usuario), EL Sistema DEBERÁ usar eager loading para evitar consultas N+1
4. EL Sistema DEBERÁ mantener anchos de columna legibles para toda la información
5. EL Sistema DEBERÁ mostrar un indicador visual si el empleado tiene acceso al sistema (user_id no nulo)

### Requisito 6: Operaciones CRUD Completas

**Historia de Usuario:** Como administrador de HR, quiero realizar operaciones completas de crear, leer, actualizar y eliminar empleados, para gestionar el personal efectivamente.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ permitir crear nuevos Empleados con todos los campos requeridos
2. EL Sistema DEBERÁ permitir editar Empleados existentes manteniendo la integridad de datos
3. EL Sistema DEBERÁ implementar eliminación lógica para Empleados usando el timestamp deleted_at
4. EL Sistema DEBERÁ permitir restaurar Empleados eliminados lógicamente
5. CUANDO un Empleado es eliminado, EL Sistema DEBERÁ preservar todos los datos de relaciones

### Requisito 7: Validaciones de Datos

**Historia de Usuario:** Como sistema, quiero validar todos los datos de empleados, para mantener la integridad y consistencia de la información.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ validar que nombre sea requerido y tenga máximo 255 caracteres
2. EL Sistema DEBERÁ validar que email sea requerido y siga el formato de email (no necesita ser único)
3. EL Sistema DEBERÁ validar que documento_numero sea único si se proporciona
4. EL Sistema DEBERÁ validar que codigo_empleado sea único si se proporciona
5. EL Sistema DEBERÁ validar que company_id exista y pertenezca al grupo actual
6. SI se proporciona location_id, EL Sistema DEBERÁ validar que exista y pertenezca a la empresa seleccionada
7. SI se proporciona user_id, EL Sistema DEBERÁ validar que el usuario exista y pertenezca al mismo tenant
8. EL Sistema DEBERÁ mostrar errores de validación en español
9. CUANDO la validación falla, EL Sistema DEBERÁ preservar la entrada del usuario para corrección

### Requisito 8: Búsqueda y Filtrado

**Historia de Usuario:** Como usuario con muchos empleados, quiero buscar y filtrar la lista, para encontrar rápidamente empleados específicos.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ proporcionar un campo de búsqueda que filtre por nombre o email
2. CUANDO se ingresa texto de búsqueda, EL Sistema DEBERÁ actualizar los resultados en tiempo real
3. EL Sistema DEBERÁ permitir filtrar por estado (activo/suspendido)
4. EL Sistema DEBERÁ permitir filtrar por empresa
5. EL Sistema DEBERÁ permitir filtrar por local (incluyendo empleados sin local asignado)
6. EL Sistema DEBERÁ mantener el estado de búsqueda y filtros durante la paginación

### Requisito 9: Gestión de Estados

**Historia de Usuario:** Como administrador, quiero gestionar el estado laboral de los empleados, para reflejar su situación actual en la empresa.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ almacenar estado como entero (1=activo, 0=suspendido, 2=cesado)
2. EL Sistema DEBERÁ permitir cambiar estado entre activo, suspendido y cesado
3. CUANDO un empleado es marcado como cesado, EL Sistema DEBERÁ requerir fecha_cese
4. EL Sistema DEBERÁ mostrar el estado visualmente en la vista de lista con colores distintivos
5. CUANDO el estado cambia, EL Sistema DEBERÁ actualizar la base de datos inmediatamente
6. EL Sistema DEBERÁ mostrar una notificación confirmando el cambio de estado

### Requisito 10: Notificaciones de Usuario

**Historia de Usuario:** Como usuario realizando operaciones, quiero recibir notificaciones claras, para confirmar que mis acciones fueron exitosas o identificar errores.

#### Criterios de Aceptación

1. CUANDO un Empleado se crea exitosamente, EL Sistema DEBERÁ mostrar una notificación de éxito
2. CUANDO un Empleado se actualiza exitosamente, EL Sistema DEBERÁ mostrar una notificación de éxito
3. CUANDO un Empleado se elimina exitosamente, EL Sistema DEBERÁ mostrar una notificación de éxito
4. CUANDO un Empleado se restaura exitosamente, EL Sistema DEBERÁ mostrar una notificación de éxito
5. CUANDO una operación falla, EL Sistema DEBERÁ mostrar una notificación de error con detalles
6. EL Sistema DEBERÁ usar el patrón dispatch('notify') para todas las notificaciones
7. EL Sistema DEBERÁ mostrar notificaciones en español

### Requisito 11: Migración de Datos Existentes

**Historia de Usuario:** Como administrador del sistema, quiero migrar datos existentes a la nueva estructura, para preservar la información histórica.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ proporcionar una migración que agregue la columna company_id y modifique location_id a nullable en hr_empleados
2. EL Sistema DEBERÁ proporcionar una migración que elimine la tabla hr_empleado_group_access
3. CUANDO se migran datos existentes, EL Sistema DEBERÁ asignar valores predeterminados o derivados para company_id
4. EL Sistema DEBERÁ manejar casos donde empleados existentes no puedan ser mapeados automáticamente
5. EL Sistema DEBERÁ registrar cualquier problema de migración de datos para revisión manual

### Requisito 12: Paginación y Rendimiento

**Historia de Usuario:** Como usuario con grandes volúmenes de datos, quiero que el listado sea rápido y manejable, para trabajar eficientemente.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ paginar la lista de empleados con elementos configurables por página
2. EL Sistema DEBERÁ usar eager loading para las relaciones de empresa y local
3. EL Sistema DEBERÁ indexar columnas de clave foránea para rendimiento de consultas
4. CUANDO se carga la lista, EL Sistema DEBERÁ ejecutar consultas eficientemente sin problemas N+1
5. EL Sistema DEBERÁ mantener el estado de paginación durante operaciones de búsqueda y filtrado

### Requisito 13: Integración con Helpers del Sistema

**Historia de Usuario:** Como desarrollador, quiero usar los helpers existentes del sistema, para mantener consistencia en el código.

#### Criterios de Aceptación

1. EL Sistema DEBERÁ usar el helper current_group() para obtener el group_company_id actual
2. EL Sistema DEBERÁ usar el helper current_group_code() cuando sea necesario para propósitos de visualización
3. EL Sistema DEBERÁ usar el helper __() para todas las cadenas traducibles
4. EL Sistema DEBERÁ seguir las convenciones de Livewire 4 del proyecto con atributos PHP 8
5. EL Sistema DEBERÁ usar el patrón de notificación del proyecto con dispatch('notify')
