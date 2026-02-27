# Requerimiento Funcional y Tecnico: ERP Catalogos (Modal Unico y Autocompletado)

**Fecha:** 2026-02-20 00:10
**Modulo:** ERP
**Componente:** Catalogos de Articulos y Servicios
**Estado:** Implementado parcialmente (base de datos + seeder + render dinamico de campos)

---

## 1. Objetivo

Definir y estandarizar el comportamiento de un modal unico para todas las categorias de catalogos ERP, incluyendo:

- Campos base del formulario.
- Campos dinamicos por categoria.
- Orden de autocompletado de `descripcion` por categoria.
- Uso de `data-codigo` en opciones `select`.
- Configuracion persistente por categoria en base de datos.

---

## 2. Alcance

Aplica al flujo de creacion/edicion de catalogos en la vista:

- `modules/ERP/Resources/views/catalogos/index.blade.php`

Con soporte de backend en:

- `modules/ERP/Http/Controllers/CatalogoController.php`

Y configuracion en base de datos:

- `modules/ERP/Database/Migrations/2026_02_20_000002_add_campos_autocompletado_to_erp_categorias_table.php`
- `modules/ERP/Database/Seeders/TodoCatalogosSeeder.php`

---

## 3. Campos base del modal

Orden visual requerido:

1. `subcategoria` (select)
2. `codigo` (input text)
3. `descripcion` (input text, autocompletable)

Campos ocultos:

- `categoria` (id de categoria)
- `table` (tabla destino)
- `id` (id de registro en edicion)
- `estado` (temporalmente oculto, default `1`)

Regla de UI:

- `categoria` no se muestra como campo editable, ya viene definida por el tab/boton que abre el modal.

---

## 4. Relacion categoria -> tab/slot

Mapeo vigente en UI/backend:

1. `MON` -> MONTURA
2. `LTE` -> LENTES TERMINADOS
3. `LST` -> LENTES SEMI TERMINADOS
4. `LCT` -> LENTES DE CONTACTO
5. `SOL` -> SOLAR
6. `EST` -> ESTUCHE
7. `LIQ` -> LIQUIDOS
8. `ACC` -> ACCESORIOS
9. `EQP` -> EQUIPOS
10. `SER` -> SERVICIO

Nota:

- El boton de cada tab envia `data-categoria-id` y `data-categoria-slot`.
- `data-categoria-id` define la categoria real para filtrar opciones por `categoria_id`.

---

## 5. Campos dinamicos por categoria (`caracteristicas`)

Cada categoria define su arreglo de campos dinamicos en `erp_categorias.caracteristicas` (JSON).

### 5.1 Matriz de caracteristicas

- `MON`: `['material','marca','tipo','talla','color','detallecolor','clase','genero','presentacion','imagen']`
- `LTE`: `['material','tipo','marca','fotocromatico','tratamiento','indice','ojobifocal','adicion','imagen']`
- `LST`: `['base','material','tipo','marca','fotocromatico','tratamiento','indice','ojobifocal','adicion','imagen']`
- `LCT`: `['material','tipo','marca','modalidad','color','detallecolor','cb','o','clase','presentacion','imagen']`
- `SOL`: `['material','marca','tipo','talla','color','colorluna','clase','genero','modelo','presentacion','imagen']`
- `EST`: `['material','modelo','marca','color','detallecolor','clase','genero','presentacion','imagen']`
- `LIQ`: `['tipo','marca','clase','presentacion','imagen']`
- `ACC`: `['modelo','marca','tipo','presentacion','imagen']`
- `EQP`: `['tipo','modelo','marca','presentacion','imagen']`
- `SER`: `['tipo','modelo']`

---

## 6. Reglas de autocompletado de descripcion (`campos_autocompletado`)

Cada categoria define su arreglo en `erp_categorias.campos_autocompletado` (JSON).

### 6.1 Matriz de autocompletado por categoria

- `MON`: `['categoria','subcategoria','material','marca','codigo','tipo','talla','color','detallecolor']`
- `LTE`: `['subcategoria','material','tipo','fotocromatico','tratamiento','diametro']`
- `LST`: `['material','tipo','fotocromatico','tratamiento','diametro','medida','adicion']`
- `LCT`: `['categoria','subcategoria','material','tipo','marca','modalidad','color']`
- `SOL`: `['categoria','subcategoria','material','marca','codigo','tipo','talla','color','colorluna']`
- `EST`: `['categoria','subcategoria','material','modelo','marca','color']`
- `LIQ`: `['categoria','subcategoria','marca']`
- `ACC`: `['subcategoria','modelo','tipo','marca']`
- `EQP`: `['subcategoria','tipo','modelo','marca']`
- `SER`: `['subcategoria','tipo','modelo']`

Regla de composicion esperada:

- La `descripcion` se arma en el orden exacto definido en `campos_autocompletado`.
- Para campos `select`, se usa el `data-codigo` del `option` seleccionado.
- Para campos de texto (ejemplo: `codigo`), se usa el valor del input.

---

## 7. Regla de codigos en selects (`data-codigo`)

Todo `select` dinamico debe incluir en cada `option`:

- `value`: id del registro catalogo.
- `data-codigo`: codigo del registro catalogo (`erp_*`.`codigo`).
- Texto visible: `nombre`.

Estado actual:

- Backend (`CatalogoController`) ya retorna `id`, `nombre`, `codigo` por opcion.
- Frontend (`index.blade.php`) ya renderiza `data-codigo` en opciones de `subcategoria` y caracteristicas dinamicas.

---

## 8. Persistencia en base de datos

### 8.1 Cambios en `erp_categorias`

Migracion:

- `modules/ERP/Database/Migrations/2026_02_20_000002_add_campos_autocompletado_to_erp_categorias_table.php`

Campos agregados:

- `campos_autocompletado` (JSON, nullable)
- `caracteristicas` (JSON, nullable)

### 8.2 Seeder

Seeder:

- `modules/ERP/Database/Seeders/TodoCatalogosSeeder.php`

Comportamiento:

- Inserta/actualiza categorias por `codigo`.
- Guarda arreglos JSON en `campos_autocompletado` y `caracteristicas`.
- Convierte arrays a JSON solo si la columna existe (compatibilidad por ambiente).

---

## 9. Flujo tecnico (resumen)

1. Usuario abre un tab y hace click en "Nuevo".
2. Boton envia `data-categoria-id`, `data-categoria-slot`, `data-categoria-nombre`.
3. Al abrir modal:
   - Se setea `categoria` y metadata.
   - Se cargan `subcategoria` y demas opciones filtradas por `categoria_id`.
   - Se renderizan campos dinamicos de la categoria.
4. Cada `select` contiene `data-codigo` en sus `option`.
5. `descripcion` debe completarse en base al orden de `campos_autocompletado`.

---

## 10. Estado de implementacion

Implementado:

- Modal unico para categorias.
- Filtrado de opciones por `categoria_id`.
- Render dinamico de caracteristicas.
- `data-codigo` en `option` de selects dinamicos.
- Persistencia de configuracion por categoria (`campos_autocompletado`, `caracteristicas`) en DB + Seeder.

Pendiente recomendado para cerrar 100%:

- Reemplazar mapeos hardcodeados JS por lectura directa de `caracteristicas` y `campos_autocompletado` desde backend.
- Implementar/ajustar el algoritmo final de autocompletado de `descripcion` basado en `data-codigo` + campos texto.

---

## 11. Comandos de despliegue local

```bash
php artisan migrate
php artisan db:seed --class="Modules\\ERP\\Database\\Seeders\\TodoCatalogosSeeder"
```

---

## 12. Criterios de aceptacion

1. Al abrir modal desde cualquier tab, `subcategoria` se carga solo con registros de su categoria.
2. Todos los `select` dinamicos muestran `option` con `data-codigo`.
3. En `erp_categorias`, cada categoria tiene JSON valido en:
   - `caracteristicas`
   - `campos_autocompletado`
4. `descripcion` puede componerse respetando el orden definido por categoria.

