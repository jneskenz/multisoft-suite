# Reglas de Nomenclatura de Documentación

**Fecha:** 03/02/2026 23:00  
**Módulo:** Gestión de Documentación  
**Estado:** ✅ Normativa Oficial

---

## 📋 Objetivo

Establecer una convención clara y consistente para el nombrado de archivos de documentación en el proyecto Multisoft Suite, garantizando trazabilidad, orden cronológico y facilidad de búsqueda.

---

## 📁 Estructura de Nombrado de Archivos

### Formato Estándar

```
YYYY-MM-DD_HHMM_nombre_descriptivo.md
```

### Componentes

| Componente | Formato | Descripción | Ejemplo |
|------------|---------|-------------|---------|
| **Fecha** | YYYY-MM-DD | Año-Mes-Día de creación del documento | 2026-02-03 |
| **Separador** | _ | Guión bajo separa fecha de hora | _ |
| **Hora** | HHMM | Hora y minutos en formato 24h | 2300 (11:00 PM) |
| **Separador** | _ | Guión bajo separa hora de nombre | _ |
| **Nombre** | snake_case | Nombre descriptivo en minúsculas con guiones bajos | sistema_idiomas |
| **Extensión** | .md | Formato Markdown | .md |

---

## ✅ Ejemplos Válidos

```
✓ 2026-02-03_2205_sistema_idiomas_opcion_a.md
✓ 2026-02-04_1300_implementacion_rol_unico_global.md
✓ 2026-02-05_0915_configuracion_base_datos.md
✓ 2026-02-10_1430_migracion_modulo_partners.md
✓ 2026-03-15_0800_actualizacion_dependencias.md
```

---

## ❌ Ejemplos Inválidos

```
✗ sistema-idiomas.md                     (falta fecha y hora)
✗ 2026-02-03-sistema-idiomas.md          (falta hora, usa guiones en lugar de guión bajo)
✗ 03-02-2026_2205_sistema_idiomas.md     (orden de fecha incorrecto)
✗ 2026-02-03_sistema_idiomas.md          (falta hora)
✗ 2026-02-03_22:05_sistema_idiomas.md    (formato de hora con dos puntos)
✗ SistemaIdiomas.md                      (PascalCase no permitido)
✗ sistema_idiomas_2026.md                (fecha al final)
```

---

## 📚 Tipos de Documentos y Convenciones

### 1. Documentos Oficiales de Arquitectura

**Convención:** Usar nombre descriptivo sin fecha-hora

```
Documento_Oficial_Arquitectura_Multisoft_Suite_v2.0.md
```

**Razón:** Estos documentos son versionados y no requieren timestamp en el nombre.

### 2. Documentos de Implementación/Cambios

**Convención:** YYYY-MM-DD_HHMM_nombre_descriptivo.md

```
2026-02-03_2205_sistema_idiomas_opcion_a.md
2026-02-04_1300_implementacion_rol_unico_global.md
```

**Razón:** Trazabilidad temporal de cambios específicos.

### 3. Documentos de Estado/Reporte

**Convención:** YYYY-MM-DD_HHMM_estado_proyecto.md

```
2026-02-01_1201_estado_proyecto.md
2026-02-15_0900_estado_proyecto.md
```

**Razón:** Snapshots del estado del proyecto en momentos específicos.

### 4. Guías y Tutoriales

**Convención:** nombre_descriptivo_guia.md (sin fecha si es permanente)

```
laravel-modular-structure.md
guia_despliegue_produccion.md
```

**Razón:** Documentos de referencia que se actualizan sin crear nuevas versiones.

### 5. Actas de Reuniones

**Convención:** YYYY-MM-DD_HHMM_reunion_tema.md

```
2026-02-05_1400_reunion_revision_arquitectura.md
2026-02-10_1000_reunion_sprint_planning.md
```

**Razón:** Registro cronológico de decisiones tomadas en reuniones.

### 6. Notas Técnicas/Decisiones

**Convención:** YYYY-MM-DD_HHMM_decision_nombre.md

```
2026-02-03_1600_decision_uso_postgresql.md
2026-02-08_0930_decision_estructura_permisos.md
```

**Razón:** Documentar decisiones técnicas importantes con contexto temporal.

---

## 📂 Estructura de Carpetas

```
docs/
├── Documento_Oficial_Arquitectura_Multisoft_Suite_v2.0.md (documento principal)
├── 2026-02-03_2205_sistema_idiomas_opcion_a.md
├── 2026-02-03_2300_reglas_nomenclatura_documentacion.md
├── 2026-02-04_1300_implementacion_rol_unico_global.md
├── laravel-modular-structure.md (guía permanente)
├── arquitectura/                (opcional: subdirectorios por tema)
│   └── 2026-02-01_1500_diseno_base_datos.md
├── implementaciones/
│   ├── 2026-02-03_2205_sistema_idiomas_opcion_a.md
│   └── 2026-02-04_1300_implementacion_rol_unico_global.md
└── reuniones/
    └── 2026-02-05_1400_reunion_revision_arquitectura.md
```

---

## 🔍 Ventajas de Esta Convención

### 1. Ordenamiento Automático
Los archivos se ordenan cronológicamente por defecto en exploradores de archivos.

### 2. Búsqueda Eficiente
Facilita búsquedas por:
- **Fecha:** `2026-02-03_*`
- **Mes:** `2026-02_*`
- **Año:** `2026_*`
- **Tema:** `*_sistema_idiomas_*`

### 3. Trazabilidad
Permite rastrear cuándo se documentó cada cambio o decisión.

### 4. Compatibilidad
Funciona correctamente en todos los sistemas operativos (Windows, Linux, macOS).

### 5. Sin Ambigüedad
No hay confusión sobre el orden de día/mes/año (problema común con formatos como DD-MM-YYYY vs MM-DD-YYYY).

---

## 🛠️ Herramientas y Automatización

### Comando Git para Listar Documentos por Fecha

```bash
# Listar todos los documentos de febrero 2026
ls docs/2026-02-*.md

# Buscar documentos sobre "sistema"
ls docs/*sistema*.md

# Ordenar por fecha (automático)
ls -l docs/2026-*.md
```

### Script de Validación de Nomenclatura

```bash
#!/bin/bash
# Validar que todos los documentos sigan la convención

for file in docs/2026-*.md; do
  if [[ ! $file =~ ^docs/[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{4}_[a-z_]+\.md$ ]]; then
    echo "❌ Nombre inválido: $file"
  else
    echo "✅ Nombre válido: $file"
  fi
done
```

### Template para Nuevos Documentos

```bash
# Crear nuevo documento con timestamp automático
NEW_DOC="docs/$(date +%Y-%m-%d_%H%M)_nombre_descriptivo.md"
touch "$NEW_DOC"
echo "Documento creado: $NEW_DOC"
```

---

## 📝 Plantilla de Documento

Cada documento debe comenzar con el siguiente header:

```markdown
# Título del Documento

**Fecha:** DD/MM/YYYY HH:MM  
**Módulo:** Nombre del módulo afectado  
**Estado:** ✅ Completado | 🚧 En Progreso | 📋 Planificado | ❌ Cancelado

---

## Descripción

[Breve descripción del contenido del documento]

## Contenido

[Contenido principal del documento]

---

**Autor:** Nombre del autor  
**Revisado por:** Nombre del revisor (si aplica)
```

---

## 🔄 Actualización de Documentos

### Opción 1: Crear Nueva Versión
Para cambios significativos, crear un nuevo documento:

```
2026-02-03_2205_sistema_idiomas_opcion_a.md  (original)
2026-02-10_1500_sistema_idiomas_opcion_b.md  (nueva versión)
```

### Opción 2: Actualizar Documento Existente
Para correcciones menores, actualizar el documento original y agregar nota:

```markdown
---
**Actualizado:** 10/02/2026 15:00  
**Cambios:** Corrección de typos y actualización de ejemplos
---
```

---

## 🎯 Casos Especiales

### Documentos Multiidioma

```
2026-02-03_2205_sistema_idiomas_es.md
2026-02-03_2205_sistema_idiomas_en.md
```

### Documentos de Series

```
2026-02-05_1000_tutorial_parte_1.md
2026-02-05_1015_tutorial_parte_2.md
2026-02-05_1030_tutorial_parte_3.md
```

### Documentos Relacionados

```
2026-02-04_1300_implementacion_rol_unico_global.md
2026-02-04_1305_migracion_rol_unico_global.sql
2026-02-04_1310_testing_rol_unico_global.md
```

---

## ✅ Checklist de Verificación

Antes de crear un nuevo documento, verificar:

- [ ] El nombre sigue el formato: `YYYY-MM-DD_HHMM_nombre_descriptivo.md`
- [ ] La fecha corresponde a la fecha de creación del documento
- [ ] La hora está en formato 24h sin separadores (HHMM)
- [ ] El nombre descriptivo usa snake_case (minúsculas con guiones bajos)
- [ ] El nombre es descriptivo y autoexplicativo
- [ ] La extensión es `.md` (Markdown)
- [ ] El documento incluye el header estándar con fecha, módulo y estado
- [ ] El contenido está bien estructurado con títulos jerárquicos
- [ ] Se ha revisado la ortografía y gramática

---

## 📊 Estadísticas de Documentación

### Comando para Contar Documentos por Mes

```bash
# Contar documentos de febrero 2026
ls docs/2026-02-*.md | wc -l

# Documentos por categoría
echo "Sistema de idiomas: $(ls docs/*idiomas*.md | wc -l)"
echo "Implementaciones: $(ls docs/*implementacion*.md | wc -l)"
```

---

## 🚀 Migración de Documentos Existentes

Para documentos que no sigan esta convención:

1. Identificar la fecha de creación original (git log)
2. Renombrar el archivo siguiendo la convención
3. Actualizar referencias en otros documentos
4. Commit con mensaje descriptivo

```bash
# Ejemplo de migración
git mv docs/sistema-idiomas.md docs/2026-02-03_2205_sistema_idiomas_opcion_a.md
git commit -m "docs: renombrar documento según convención de nomenclatura"
```

---

## 📚 Referencias

- [Markdown Guide](https://www.markdownguide.org/)
- [ISO 8601 Date Format](https://en.wikipedia.org/wiki/ISO_8601)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

**Aprobado por:** Equipo de Arquitectura Multisoft Suite  
**Vigencia:** A partir del 03/02/2026  
**Revisión:** Anual o cuando sea necesario

---

*Este documento es parte del sistema de documentación de Multisoft Suite v2.0*
