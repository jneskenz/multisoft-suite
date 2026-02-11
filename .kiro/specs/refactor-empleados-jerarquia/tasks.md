# Plan de Implementación

## Tareas

### 1. Preparación y Migraciones de Base de Datos

- [ ] 1.1 Crear migración para modificar tabla hr_empleados
  - Agregar columna `user_id` (nullable, FK a users)
  - Eliminar columna `password`
  - Modificar columna `email` (remover unique constraint)
  - Agregar columnas: `documento_tipo`, `documento_numero`, `telefono`, `codigo_empleado`, `cargo`, `fecha_ingreso`, `fecha_cese`
  - Agregar unique constraints para `documento_numero` y `codigo_empleado`
  - Agregar índices necesarios

- [ ] 1.2 Crear migración para eliminar tabla hr_empleado_group_access
  - Drop tabla `hr_empleado_group_access`
  - Verificar que no haya dependencias

- [ ] 1.3 Ejecutar migraciones en entorno de desarrollo
  - Hacer backup de datos existentes
  - Ejecutar migraciones
  - Verificar estructura de tablas

### 2. Actualizar Modelos

- [ ] 2.1 Actualizar modelo Empleado
  - Actualizar array `$fillable` con nuevos campos
  - Eliminar `password` de `$hidden`
  - Actualizar `$casts` para incluir fechas
  - Agregar relación `belongsTo(User::class)` opcional
  - Mantener relaciones existentes (tenant, group, company, location)
  - Actualizar constantes de estado (agregar ESTADO_CESADO = 2)

- [ ] 2.2 Eliminar modelo EmpleadoGroupAccess
  - Eliminar archivo `modules/HR/Models/EmpleadoGroupAccess.php`
  - Verificar que no haya referencias en el código

- [ ] 2.3 Actualizar scopes en modelo Empleado
  - Verificar scope `forCurrentGroup`
  - Agregar scope `active()`, `suspended()`, `cesado()`
  - Mantener trait `BelongsToTenant` si existe

### 3. Actualizar Componente Livewire EmpleadoManager

- [x] 3.1 Actualizar propiedades del componente
  - Eliminar propiedades: `password`, `password_confirmation`, `selectedGroups`
  - Agregar propiedades: `documento_tipo`, `documento_numero`, `telefono`, `codigo_empleado`, `cargo`, `fecha_ingreso`, `user_id`
  - Actualizar valores por defecto

- [x] 3.2 Actualizar método `rules()`
  - Eliminar validaciones de password
  - Agregar validaciones para nuevos campos
  - Validar unicidad de `documento_numero` y `codigo_empleado`
  - Actualizar mensajes de validación en español

- [x] 3.3 Actualizar método `save()`
  - Eliminar lógica de hash de password
  - Eliminar llamada a `syncGroupAccess()`
  - Agregar asignación de nuevos campos
  - Mantener asignación automática de `tenant_id` y `group_company_id`

- [x] 3.4 Actualizar método `edit()`
  - Cargar nuevos campos del empleado
  - Eliminar carga de `selectedGroups`

- [x] 3.5 Eliminar métodos relacionados con grupos
  - Eliminar computed property `availableGroups`
  - Eliminar cualquier lógica de sincronización de grupos

- [x] 3.6 Actualizar método `empleados()` (query)
  - Agregar eager loading para relación `user`
  - Mantener eager loading de `company` y `location`
  - Actualizar búsqueda para incluir nuevos campos (documento, codigo_empleado)

### 4. Actualizar Vista Blade

- [x] 4.1 Actualizar formulario modal
  - Eliminar campos de password y confirmación
  - Eliminar sección de selección de grupos
  - Agregar campo: Tipo de documento (select: DNI, CE, Pasaporte)
  - Agregar campo: Número de documento (input text)
  - Agregar campo: Teléfono (input tel)
  - Agregar campo: Código de empleado (input text)
  - Agregar campo: Cargo (input text)
  - Agregar campo: Fecha de ingreso (input date)

- [x] 4.2 Actualizar tabla de listado
  - Agregar columna: Código de empleado
  - Agregar columna: Documento
  - Agregar columna: Cargo
  - Actualizar columna de estado para mostrar 3 estados (activo/suspendido/cesado)
  - Eliminar columna de grupos
  - Agregar indicador visual si tiene acceso al sistema (user_id)

- [x] 4.3 Actualizar filtros
  - Mantener filtros existentes (empresa, local, estado)
  - Actualizar filtro de estado para incluir "cesado"
  - Agregar filtro por cargo (opcional)

### 5. Actualizar Traducciones

- [ ] 5.1 Agregar traducciones en español (lang/es.json)
  - Agregar textos para nuevos campos
  - Agregar textos para estados (activo, suspendido, cesado)
  - Agregar mensajes de validación

- [ ] 5.2 Agregar traducciones en inglés (lang/en.json)
  - Traducir todos los textos nuevos

### 6. Testing

- [ ] 6.1 Crear/actualizar tests unitarios
  - Test: Crear empleado sin password
  - Test: Crear empleado con user_id opcional
  - Test: Validar unicidad de documento_numero
  - Test: Validar unicidad de codigo_empleado
  - Test: Validar que email no necesita ser único
  - Test: Filtrado por grupo funciona correctamente
  - Test: Relación con User funciona correctamente

- [ ] 6.2 Crear/actualizar tests de integración
  - Test: CRUD completo de empleados
  - Test: Búsqueda por nuevos campos
  - Test: Filtrado por estado (incluyendo cesado)
  - Test: Eager loading funciona sin N+1

- [ ] 6.3 Testing manual
  - Crear empleado con todos los campos
  - Editar empleado existente
  - Cambiar estados (activo → suspendido → cesado)
  - Verificar búsqueda por documento y código
  - Verificar filtros funcionan correctamente

### 7. Documentación

- [ ] 7.1 Actualizar README del módulo HR
  - Documentar nueva estructura de empleados
  - Documentar campos obligatorios vs opcionales
  - Documentar relación con users

- [ ] 7.2 Crear/actualizar diagramas
  - Diagrama ER actualizado con nueva estructura
  - Diagrama de flujo de creación de empleado

- [ ] 7.3 Documentar migración de datos
  - Instrucciones para migrar datos existentes
  - Script de migración si es necesario

### 8. Limpieza y Optimización

- [ ] 8.1 Eliminar código obsoleto
  - Buscar y eliminar referencias a `hr_empleado_group_access`
  - Buscar y eliminar referencias a `selectedGroups`
  - Buscar y eliminar validaciones de password en empleados

- [ ] 8.2 Optimizar queries
  - Verificar índices están creados
  - Verificar eager loading funciona
  - Medir performance de listado

- [ ] 8.3 Code review
  - Revisar convenciones de código
  - Revisar seguridad
  - Revisar accesibilidad de la UI

### 9. Despliegue

- [ ] 9.1 Preparar entorno de staging
  - Backup de base de datos
  - Ejecutar migraciones
  - Verificar funcionalidad

- [ ] 9.2 Desplegar a producción
  - Backup de base de datos de producción
  - Ejecutar migraciones
  - Verificar funcionalidad
  - Monitorear errores

- [ ] 9.3 Post-despliegue
  - Verificar logs
  - Verificar performance
  - Recopilar feedback de usuarios

## Notas de Implementación

### Orden Recomendado
1. Empezar con migraciones (1.1, 1.2, 1.3)
2. Actualizar modelos (2.1, 2.2, 2.3)
3. Actualizar Livewire (3.1 - 3.6)
4. Actualizar vistas (4.1 - 4.3)
5. Traducciones (5.1, 5.2)
6. Testing (6.1 - 6.3)
7. Documentación (7.1 - 7.3)
8. Limpieza (8.1 - 8.3)
9. Despliegue (9.1 - 9.3)

### Consideraciones Importantes

**Migración de Datos Existentes:**
- Los empleados existentes tendrán `user_id = NULL` (no tienen acceso al sistema)
- Los empleados existentes necesitarán que se les asigne `documento_numero`, `codigo_empleado`, etc. manualmente o mediante script
- Considerar crear un comando Artisan para migración de datos si hay muchos registros

**Relación con Users:**
- Si un empleado necesita acceso al sistema, crear primero el `User` en Core
- Luego vincular el `user_id` en el empleado
- El email del User debe ser único, pero el email del Empleado no

**Estados:**
- Estado 1 (Activo): Empleado trabajando normalmente
- Estado 0 (Suspendido): Empleado temporalmente sin actividad
- Estado 2 (Cesado): Empleado que ya no trabaja en la empresa (requiere fecha_cese)

**Performance:**
- Usar eager loading siempre: `->with(['company', 'location', 'user'])`
- Los índices en documento_numero y codigo_empleado mejorarán búsquedas
- Considerar caché para listas de empresas y locales si son muchas

**Seguridad:**
- Validar siempre que company_id pertenezca al grupo actual
- Validar siempre que location_id pertenezca a la empresa seleccionada
- Si se proporciona user_id, validar que pertenezca al mismo tenant
