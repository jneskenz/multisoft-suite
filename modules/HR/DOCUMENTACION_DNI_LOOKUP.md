# Integración de Búsqueda de DNI en Módulo HR

## 📋 Resumen de cambios

Se ha integrado la función `lookup_dni()` del módulo Core en el formulario de registro de empleados del módulo HR, permitiendo buscar automáticamente los datos de una persona al ingresar su DNI.

## 🔧 Cambios realizados

### 1. **EmpleadoManager.php** (Livewire Component)
- ✅ Método `searchDni()` - Consulta la API y rellena nombres/apellidos
- ✅ Método `updatedDocumentoNumero()` - Listener que dispara búsqueda automática
- ✅ Método `updatedDocumentoTipo()` - Limpia documento cuando cambia tipo
- ✅ Validaciones:
  - Formato DNI (8 dígitos)
  - DNI no existe en el sistema
  - Respuesta de API exitosa

### 2. **empleado-manager.blade.php** (Vista)
- ✅ Cambio de `wire:model` a `wire:model.change` en tipo de documento
- ✅ Cambio de `wire:model` a `wire:model` + `wire:change` en número de documento
- ✅ Deshabilitación de campos en modo edición
- ✅ Botón manual para buscar DNI (con indicador de carga)
- ✅ Badge informativo cuando se puede buscar

## 🚀 Flujo de uso

1. **Usuario selecciona "DNI"** en el campo "Tipo de documento"
2. **Usuario digita el DNI** (8 dígitos)
3. **Automáticamente al completar 8 dígitos:**
   - Se valida formato
   - Se verifica que no exista en BD
   - Se consulta API `lookup_dni()`
   - Se rellenan nombres y apellidos automáticamente
4. **Usuario puede:**
   - Editar los datos obtenidos
   - Continuar con los demás campos
   - Guardar el empleado

## ⚙️ Configuración requerida

En archivo `.env`:
```env
DOCUMENT_LOOKUP_URL=https://api.apis.net.pe/v2
DOCUMENT_LOOKUP_TOKEN=tu_token_aqui
DOCUMENT_LOOKUP_TIMEOUT=10
DOCUMENT_LOOKUP_CACHE_TTL=3600
```

## 🎯 Nota importante

La búsqueda **solo funciona en modo de creación**, no en edición. Esto es por seguridad para evitar cambios inesperados de datos.

## 📚 Archivos modificados

- `modules/HR/Livewire/EmpleadoManager.php`
- `modules/HR/Resources/views/livewire/empleado-manager.blade.php`

## 🔍 Cómo reutilizarlo en otros módulos

Para usar la función en otros módulos, simplemente importa y usa:

```php
// En un Livewire component
use Modules\HR\Livewire\EmpleadoManager;

$resultado = lookup_dni('12345678');
if ($resultado['success']) {
    $datos = $resultado['data'];
    // Usar datos...
}
```

O a través de Facade:
```php
use Modules\Core\Facades\DocumentLookup;

$resultado = DocumentLookup::lookupDni('12345678');
```
