# Documentación de StockSystem

Documentación técnica y funcional del proyecto. Para una introducción rápida y los pasos de instalación mínimos, ver el [README principal](../README.md).

| Documento | Contenido |
|-----------|-----------|
| [Instalación y configuración](instalacion.md) | Requisitos, instalación local, Docker, variables de entorno, usuarios de prueba, despliegue. |
| [Arquitectura](arquitectura.md) | Estructura de carpetas, capas (controladores, servicios, modelos), middleware, eventos, frontend, PDF y Excel. |
| [Modelo de datos](modelo-de-datos.md) | Diagrama entidad-relación, tablas, columnas, códigos y borrados en cascada. |
| [Flujos de negocio](flujos-de-negocio.md) | Ciclo de vida del remito, impacto en el stock, tránsito, portal del técnico, alertas, dashboard, reportes e importación CSV. |
| [Roles y permisos](roles-y-permisos.md) | Roles, permisos por operación, selección de operación y gestión de usuarios. |
| [Rutas](rutas.md) | Referencia de todas las rutas web y de la API. |
| [Problemas conocidos](problemas-conocidos.md) | Bugs, riesgos de seguridad y deuda técnica detectados al documentar. |

## Glosario

| Término | Significado en el sistema |
|---------|---------------------------|
| **Operación** | Unidad de negocio o empresa (por ejemplo `POP36`, `POP20`). Agrupa centros de stock y define qué ve cada usuario. Cada operación tiene un permiso homónimo. |
| **Centro de stock** (`stockcenter`) | Cualquier lugar que tiene o recibe mercadería: depósito, móvil de un técnico, taller, proveedor o consumo. |
| **Artículo** | Ítem del catálogo (material, herramienta, insumo). Se mide en unidades, metros o kilogramos. |
| **Stock** | Cantidad de un artículo en un centro de stock. |
| **Remito** (`refer`) | Documento que traslada artículos de un centro de origen a uno de destino. Es la única forma de modificar el stock. |
| **Movimiento** (`movement`) | Cada renglón (artículo + cantidad) de un remito. |
| **En tránsito** | Mercadería de un remito emitido que todavía no fue recibida (finalizada) en el destino. |
| **Despachante** (`dispatcher`) | Usuario que gestiona remitos y stock de las operaciones que tiene asignadas. |
| **Técnico** (`tecnico`) | Usuario de campo con un almacén móvil asignado; solicita materiales y reporta consumos. |
