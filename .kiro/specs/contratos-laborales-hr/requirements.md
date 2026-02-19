# Documento de Requisitos - Contratos Laborales HR

## Introducción

Este documento especifica los requisitos para el módulo de Gestión de Contratos Laborales dentro del sistema HR de Multisoft Suite. El módulo permitirá gestionar el ciclo de vida completo de los contratos laborales de los empleados, incluyendo creación, renovación, terminación y seguimiento de vigencia. El sistema utilizará el **Sistema Generalizado de Plantillas de Documentos HR** para la generación de documentos contractuales.

## Glosario

- **Sistema_HR**: El módulo de Recursos Humanos de Multisoft Suite
- **Contrato_Laboral**: Documento legal que establece la relación laboral entre empleado y empresa
- **Empleado**: Persona registrada en la tabla hr_empleados
- **Estado_Contrato**: Clasificación del estado actual del contrato (Borrador, Activo, Vencido, Renovado, Terminado)
- **Contrato_Activo**: Contrato con estado "Activo" y dentro de su período de vigencia
- **Tenant**: Organización o empresa dentro del sistema multi-tenant
- **Group_Company**: Grupo empresarial al que pertenece el tenant
- **Plantilla_Documento**: Plantilla HTML parametrizable para generar documentos contractuales
- **Tipo_Contrato**: Categoría de contrato (Indefinido, Temporal, Prácticas, etc.)

## Requisitos

### Requisito 1: Crear Contratos Laborales

**Historia de Usuario:** Como usuario de HR, quiero crear contratos laborales para empleados, para formalizar la relación laboral y mantener un registro oficial.

#### Criterios de Aceptación

1. CUANDO un usuario crea un contrato laboral, EL Sistema_HR DEBERÁ validar que el empleado existe en hr_empleados
2. CUANDO un usuario crea un contrato con estado "Activo", EL Sistema_HR DEBERÁ verificar que el empleado no tenga otro Contrato_Activo
3. CUANDO un usuario crea un contrato, EL Sistema_HR DEBERÁ requerir: empleado_id, tipo_contrato_id, fecha_inicio, salario_base, moneda, jornada_laboral, cargo
4. CUANDO un usuario especifica fecha_fin, EL Sistema_HR DEBERÁ validar que fecha_fin sea posterior a fecha_inicio
5. CUANDO un usuario especifica salario_base, EL Sistema_HR DEBERÁ validar que sea mayor a cero
6. CUANDO un usuario crea un Contrato_Activo para un empleado con contratos previos activos, EL Sistema_HR DEBERÁ cambiar el estado de los contratos previos a "Renovado" o "Terminado"
7. CUANDO un usuario crea un contrato, EL Sistema_HR DEBERÁ asignar automáticamente tenant_id y group_company_id del contexto actual
8. CUANDO un usuario crea un contrato, EL Sistema_HR DEBERÁ generar automáticamente el documento contractual desde la plantilla correspondiente al tipo de contrato

### Requisito 2: Consultar y Filtrar Contratos

**Historia de Usuario:** Como usuario de HR, quiero consultar y filtrar contratos laborales, para encontrar información específica y gestionar el inventario de contratos.

#### Criterios de Aceptación

1. CUANDO un usuario accede al listado de contratos, EL Sistema_HR DEBERÁ mostrar todos los contratos del tenant actual con paginación
2. CUANDO un usuario aplica filtro por empleado, EL Sistema_HR DEBERÁ mostrar solo los contratos del empleado seleccionado
3. CUANDO un usuario aplica filtro por tipo de contrato, EL Sistema_HR DEBERÁ mostrar solo los contratos que coincidan con el tipo seleccionado
4. CUANDO un usuario aplica filtro por estado, EL Sistema_HR DEBERÁ mostrar solo los contratos con el estado seleccionado
5. CUANDO un usuario aplica filtro por vigencia, EL Sistema_HR DEBERÁ mostrar solo los contratos cuya fecha_inicio y fecha_fin incluyan el rango especificado
6. CUANDO un usuario realiza búsqueda por texto, EL Sistema_HR DEBERÁ buscar en campos: nombre_empleado, cargo, departamento, numero_contrato
7. CUANDO un usuario ordena por columna, EL Sistema_HR DEBERÁ reordenar los resultados según la columna y dirección especificadas
8. CUANDO un usuario visualiza el listado, EL Sistema_HR DEBERÁ mostrar indicadores visuales de contratos próximos a vencer (dentro de 30 días)

### Requisito 3: Editar Contratos Laborales

**Historia de Usuario:** Como usuario de HR, quiero editar contratos laborales existentes, para corregir errores o actualizar información.

#### Criterios de Aceptación

1. CUANDO un usuario edita un contrato, EL Sistema_HR DEBERÁ permitir modificar todos los campos excepto empleado_id y numero_contrato
2. CUANDO un usuario cambia el estado a "Activo" en un contrato, EL Sistema_HR DEBERÁ validar que el empleado no tenga otro Contrato_Activo
3. CUANDO un usuario modifica fecha_fin, EL Sistema_HR DEBERÁ validar que sea posterior a fecha_inicio
4. CUANDO un usuario modifica salario_base, EL Sistema_HR DEBERÁ validar que sea mayor a cero
5. CUANDO un usuario guarda cambios en un contrato, EL Sistema_HR DEBERÁ registrar la auditoría de cambios con usuario, fecha y campos modificados
6. CUANDO un usuario modifica datos del contrato, EL Sistema_HR DEBERÁ ofrecer regenerar el documento contractual con los nuevos datos

### Requisito 4: Eliminar Contratos Laborales

**Historia de Usuario:** Como usuario de HR, quiero eliminar contratos laborales, para mantener limpia la base de datos y ocultar registros obsoletos.

#### Criterios de Aceptación

1. CUANDO un usuario elimina un contrato, EL Sistema_HR DEBERÁ aplicar soft delete (deleted_at)
2. CUANDO un usuario elimina un contrato, EL Sistema_HR DEBERÁ mantener el registro en la base de datos
3. CUANDO un usuario consulta contratos, EL Sistema_HR DEBERÁ excluir los contratos eliminados (soft deleted)
4. CUANDO un usuario con permisos de administrador consulta, EL Sistema_HR DEBERÁ permitir ver contratos eliminados si se solicita explícitamente
5. CUANDO un usuario elimina un contrato, EL Sistema_HR DEBERÁ mantener el documento generado asociado para auditoría

### Requisito 5: Ver Detalle de Contrato

**Historia de Usuario:** Como usuario de HR, quiero ver el detalle completo de un contrato, para revisar toda la información y el historial de cambios.

#### Criterios de Aceptación

1. CUANDO un usuario accede al detalle de un contrato, EL Sistema_HR DEBERÁ mostrar todos los campos del contrato
2. CUANDO un usuario accede al detalle de un contrato, EL Sistema_HR DEBERÁ mostrar la información completa del empleado asociado
3. CUANDO un usuario accede al detalle de un contrato, EL Sistema_HR DEBERÁ mostrar el historial de cambios (auditoría)
4. CUANDO un usuario accede al detalle y existe documento generado, EL Sistema_HR DEBERÁ permitir descargar el archivo PDF
5. CUANDO un usuario accede al detalle de un contrato, EL Sistema_HR DEBERÁ mostrar indicadores visuales del estado mediante badges de colores
6. CUANDO un usuario accede al detalle, EL Sistema_HR DEBERÁ mostrar el estado de firmas del documento (pendiente, firmado, rechazado)

### Requisito 6: Renovar Contratos

**Historia de Usuario:** Como usuario de HR, quiero renovar contratos existentes, para crear nuevos contratos basados en contratos anteriores y mantener continuidad laboral.

#### Criterios de Aceptación

1. CUANDO un usuario renueva un contrato, EL Sistema_HR DEBERÁ crear un nuevo contrato copiando los datos del contrato original
2. CUANDO un usuario renueva un contrato, EL Sistema_HR DEBERÁ establecer el estado del nuevo contrato como "Borrador"
3. CUANDO un usuario renueva un contrato, EL Sistema_HR DEBERÁ permitir modificar fecha_inicio, fecha_fin y salario_base antes de guardar
4. CUANDO un usuario activa un contrato renovado, EL Sistema_HR DEBERÁ cambiar el estado del contrato original a "Renovado"
5. CUANDO un usuario renueva un contrato, EL Sistema_HR DEBERÁ mantener la referencia al contrato original (contrato_renovado_desde_id)
6. CUANDO un usuario renueva un contrato, EL Sistema_HR DEBERÁ generar un nuevo documento contractual con numeración consecutiva

### Requisito 7: Terminar Contratos Anticipadamente

**Historia de Usuario:** Como usuario de HR, quiero terminar contratos antes de su fecha de fin, para registrar finalizaciones anticipadas de relaciones laborales.

#### Criterios de Aceptación

1. CUANDO un usuario termina un contrato anticipadamente, EL Sistema_HR DEBERÁ cambiar el estado a "Terminado"
2. CUANDO un usuario termina un contrato anticipadamente, EL Sistema_HR DEBERÁ registrar la fecha de terminación real
3. CUANDO un usuario termina un contrato anticipadamente, EL Sistema_HR DEBERÁ requerir una observación explicando el motivo
4. CUANDO un usuario termina un contrato anticipadamente, EL Sistema_HR DEBERÁ mantener las fechas originales del contrato
5. CUANDO un usuario termina un contrato, EL Sistema_HR DEBERÁ registrar el usuario y fecha de la acción en la auditoría
6. CUANDO un usuario termina un contrato, EL Sistema_HR DEBERÁ actualizar el estado del empleado a "Cesado" si corresponde

### Requisito 8: Alertas de Vencimiento

**Historia de Usuario:** Como usuario de HR, quiero recibir alertas de contratos próximos a vencer, para tomar acciones oportunas de renovación o terminación.

#### Criterios de Aceptación

1. CUANDO el sistema evalúa contratos activos, EL Sistema_HR DEBERÁ identificar contratos cuya fecha_fin esté dentro de los próximos 30 días
2. CUANDO un contrato está próximo a vencer, EL Sistema_HR DEBERÁ mostrar una alerta visual en el listado de contratos
3. CUANDO un usuario accede al dashboard de HR, EL Sistema_HR DEBERÁ mostrar un contador de contratos próximos a vencer
4. CUANDO la fecha actual supera la fecha_fin de un Contrato_Activo, EL Sistema_HR DEBERÁ cambiar automáticamente el estado a "Vencido"
5. CUANDO un contrato está próximo a vencer, EL Sistema_HR DEBERÁ enviar notificación al responsable de HR

### Requisito 9: Exportar Listado

**Historia de Usuario:** Como usuario de HR, quiero exportar el listado de contratos a Excel, para realizar análisis externos y compartir información.

#### Criterios de Aceptación

1. CUANDO un usuario solicita exportar a Excel, EL Sistema_HR DEBERÁ generar un archivo con todos los contratos visibles según filtros aplicados
2. CUANDO un usuario exporta a Excel, EL Sistema_HR DEBERÁ incluir columnas: numero_contrato, empleado, tipo_contrato, fecha_inicio, fecha_fin, salario_base, moneda, cargo, departamento, estado
3. CUANDO un usuario exporta a Excel, EL Sistema_HR DEBERÁ aplicar formato de fecha legible (dd/mm/yyyy)
4. CUANDO un usuario exporta a Excel, EL Sistema_HR DEBERÁ aplicar formato de moneda al salario_base
5. CUANDO un usuario exporta a Excel, EL Sistema_HR DEBERÁ descargar el archivo automáticamente con nombre descriptivo

### Requisito 10: Generar y Regenerar Documento de Contrato

**Historia de Usuario:** Como usuario de HR, quiero generar documentos de contrato en PDF desde plantillas, para imprimir y formalizar la firma del contrato.

#### Criterios de Aceptación

1. CUANDO un usuario crea un contrato, EL Sistema_HR DEBERÁ generar automáticamente el documento PDF desde la plantilla del tipo de contrato
2. CUANDO un usuario genera documento PDF, EL Sistema_HR DEBERÁ reemplazar todas las variables de la plantilla con datos reales del contrato y empleado
3. CUANDO un usuario genera documento PDF, EL Sistema_HR DEBERÁ aplicar plantilla con logo y formato corporativo del grupo empresarial
4. CUANDO un usuario genera documento PDF, EL Sistema_HR DEBERÁ incluir espacios para firmas (empleado y empleador)
5. CUANDO un usuario regenera un documento, EL Sistema_HR DEBERÁ crear una nueva versión y mantener el historial de versiones
6. CUANDO un usuario descarga el documento, EL Sistema_HR DEBERÁ registrar la descarga en la auditoría
7. CUANDO un usuario genera el documento, EL Sistema_HR DEBERÁ almacenar el contenido HTML generado en hr_documentos_generados

### Requisito 11: Control de Permisos

**Historia de Usuario:** Como administrador del sistema, quiero controlar el acceso a las funcionalidades de contratos, para garantizar seguridad y segregación de funciones.

#### Criterios de Aceptación

1. CUANDO un usuario intenta ver contratos, EL Sistema_HR DEBERÁ verificar el permiso "hr.contracts.view"
2. CUANDO un usuario intenta crear un contrato, EL Sistema_HR DEBERÁ verificar el permiso "hr.contracts.create"
3. CUANDO un usuario intenta editar un contrato, EL Sistema_HR DEBERÁ verificar el permiso "hr.contracts.edit"
4. CUANDO un usuario intenta eliminar un contrato, EL Sistema_HR DEBERÁ verificar el permiso "hr.contracts.delete"
5. CUANDO un usuario intenta generar documentos, EL Sistema_HR DEBERÁ verificar el permiso "hr.contracts.generate"
6. SI un usuario no tiene el permiso requerido, ENTONCES EL Sistema_HR DEBERÁ denegar el acceso y mostrar mensaje de error

### Requisito 12: Auditoría de Cambios

**Historia de Usuario:** Como auditor del sistema, quiero revisar el historial de cambios en contratos, para garantizar trazabilidad y cumplimiento normativo.

#### Criterios de Aceptación

1. CUANDO un usuario crea un contrato, EL Sistema_HR DEBERÁ registrar: usuario, fecha, hora, y todos los valores iniciales
2. CUANDO un usuario modifica un contrato, EL Sistema_HR DEBERÁ registrar: usuario, fecha, hora, campos modificados, valores anteriores y nuevos valores
3. CUANDO un usuario elimina un contrato, EL Sistema_HR DEBERÁ registrar: usuario, fecha, hora de la eliminación
4. CUANDO un usuario consulta el historial de un contrato, EL Sistema_HR DEBERÁ mostrar todos los cambios en orden cronológico descendente
5. CUANDO un usuario consulta el historial, EL Sistema_HR DEBERÁ mostrar el nombre completo del usuario que realizó cada cambio
6. CUANDO un usuario genera o regenera un documento, EL Sistema_HR DEBERÁ registrar la acción en la auditoría

### Requisito 13: Validación de Unicidad de Contrato Activo

**Historia de Usuario:** Como usuario de HR, quiero que el sistema garantice que un empleado solo tenga un contrato activo, para evitar inconsistencias en la gestión laboral.

#### Criterios de Aceptación

1. PARA TODOS los empleados, EL Sistema_HR DEBERÁ permitir solo un contrato con estado "Activo" simultáneamente
2. CUANDO el sistema valida contratos activos, EL Sistema_HR DEBERÁ considerar tanto el estado como las fechas de vigencia
3. CUANDO un usuario intenta activar un segundo contrato, EL Sistema_HR DEBERÁ rechazar la operación y mostrar mensaje de error
4. CUANDO el sistema detecta múltiples contratos activos para un empleado, EL Sistema_HR DEBERÁ registrar una alerta en el log del sistema

### Requisito 14: Gestión de Tipos de Contrato

**Historia de Usuario:** Como administrador de HR, quiero gestionar los tipos de contrato disponibles, para adaptar el sistema a las necesidades legales y operativas de la empresa.

#### Criterios de Aceptación

1. CUANDO un administrador accede a configuración, EL Sistema_HR DEBERÁ mostrar el catálogo de tipos de contrato (hr_tipos_documento con categoria='contractual')
2. CUANDO un administrador crea un tipo de contrato, EL Sistema_HR DEBERÁ requerir: código, nombre, categoría, plantilla asociada
3. CUANDO un administrador activa/desactiva un tipo, EL Sistema_HR DEBERÁ actualizar el campo esta_activo
4. CUANDO un usuario crea un contrato, EL Sistema_HR DEBERÁ mostrar solo los tipos de contrato activos
5. CUANDO un tipo de contrato tiene plantilla asociada, EL Sistema_HR DEBERÁ usar esa plantilla para generar documentos

### Requisito 15: Numeración Automática de Contratos

**Historia de Usuario:** Como usuario de HR, quiero que los contratos tengan numeración automática y consecutiva, para mantener un control ordenado y evitar duplicados.

#### Criterios de Aceptación

1. CUANDO un usuario crea un contrato, EL Sistema_HR DEBERÁ generar automáticamente el número de contrato
2. CUANDO el sistema genera el número, EL Sistema_HR DEBERÁ usar el formato configurado en el tipo de contrato (ej: CONT-2026-001)
3. CUANDO el sistema genera el número, EL Sistema_HR DEBERÁ garantizar que sea único y consecutivo por año
4. CUANDO un usuario visualiza el contrato, EL Sistema_HR DEBERÁ mostrar el número de contrato de forma prominente
5. CUANDO un usuario busca contratos, EL Sistema_HR DEBERÁ permitir búsqueda por número de contrato
