# StockSystem

Sistema web interno para controlar inventario en varios almacenes, pensado para empresas con equipos técnicos en la calle. Registra dónde está cada artículo (depósitos, móviles de técnicos, talleres) y cada traslado se documenta con un **remito**.

> El sistema maneja solo cantidades físicas: no registra precios ni valores monetarios.

## Funcionalidades

- **Datos maestros**: artículos, centros de stock, personas y direcciones, con alta manual o importación CSV.
- **Remitos** con ciclo *Ingresado → Emitido → Finalizado / Cancelado*: al emitir se descuenta el origen y al finalizar se acredita el destino.
- **Mercadería en tránsito**: remitos emitidos pendientes de recepción.
- **Stock por centro** con niveles de alerta y notificaciones internas.
- **Dashboard** con rotación de stock, stock sin movimiento, tiempos de despacho y recomendaciones.
- **Reportes**: PDF (stock, remito) y Excel (stock, movimientos, consumo por artículo).
- **Roles y permisos por operación** (admin, despachante, técnico) con Spatie Permission.
- **Portal del técnico**: stock de su móvil, solicitud de materiales y reporte de consumo diario.

## Tecnologías

| Capa | Tecnología |
|------|------------|
| Backend | PHP ≥ 8.1, **Laravel 9** |
| Base de datos | MySQL 8 / MariaDB |
| Frontend | Blade, Bootstrap 5, jQuery, Sass, Vite 3, Boxicons, Bootstrap Icons, Chart.js |
| Paquetes | `spatie/laravel-permission`, `barryvdh/laravel-dompdf`, `maatwebsite/excel`, `laravel/ui`, `laravel/sanctum`, `tymon/jwt-auth` |
| Infraestructura | Docker Compose (PHP-FPM 8.2, Nginx, MySQL 8) |

## Inicio rápido

### Local

```bash
git clone https://github.com/facundomassa/stock_system_laravel.git
cd stock_system_laravel
composer install && npm install
cp .env.example .env && php artisan key:generate
# configurar DB_* en .env y crear la base vacía
php artisan migrate --seed
npm run dev          # en otra terminal: php artisan serve
```

Abrir http://127.0.0.1:8000.

### Docker

```bash
# en .env: DB_HOST=db, DB_DATABASE=stock_system_laravel, DB_USERNAME=root, DB_PASSWORD=secret
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
npm install && npm run build   # en el host
```

Abrir http://localhost:8000.

Detalles, variables de entorno y despliegue: [docs/instalacion.md](docs/instalacion.md).

### Usuarios de prueba

| Email | Contraseña | Rol |
|-------|------------|-----|
| `admin@admin.com` | `admin` | admin |
| `dispatcher36@dispatcher.com` | `dispatcher36` | dispatcher (operación POP36) |
| `dispatcher20@dispatcher.com` | `dispatcher20` | dispatcher (operación POP20) |
| `tecnico@tecnico.com` | `tecnico` | tecnico (operación POP36) |

Son solo para desarrollo. El seeder no crea centros de stock ni artículos; los primeros pasos están en la [guía de instalación](docs/instalacion.md#usuarios-de-prueba).

## Documentación

| Documento | Contenido |
|-----------|-----------|
| [Instalación y configuración](docs/instalacion.md) | Requisitos, local, Docker, variables de entorno, despliegue. |
| [Arquitectura](docs/arquitectura.md) | Capas, servicios, middleware, eventos, frontend, PDF/Excel. |
| [Modelo de datos](docs/modelo-de-datos.md) | Diagrama ER, tablas, códigos, cascadas. |
| [Flujos de negocio](docs/flujos-de-negocio.md) | Remitos, impacto en el stock, tránsito, portal técnico, dashboard, reportes, importación. |
| [Roles y permisos](docs/roles-y-permisos.md) | Roles, permisos por operación, gestión de usuarios. |
| [Rutas](docs/rutas.md) | Referencia de rutas web y API. |
| [Problemas conocidos](docs/problemas-conocidos.md) | Bugs, riesgos de seguridad y deuda técnica. |

Para usuarios finales hay un manual dentro de la aplicación, en **Cómo usar** (`/howtouse`).

## Estado del proyecto

| Módulo | Estado | Notas |
|--------|--------|-------|
| Artículos, personas, direcciones, centros de stock | Funcional | Incluye importación CSV. |
| Stock por centro y alertas | Funcional | Los filtros rápidos del dashboard no filtran. |
| Remitos y movimientos | Funcional | Cancelar un remito emitido no devuelve el stock al origen (ver problemas conocidos). |
| Movimientos en tránsito | Funcional | |
| Usuarios, roles y permisos | Funcional | El registro público está cerrado: los usuarios los crea un admin desde el panel. |
| Filtro por operación | Parcial | Solo se aplica en el listado de remitos; falta un *scope* global. |
| Portal del técnico | Parcial | Solicitud y consumo funcionan; falta el historial por técnico y la validación por despachante. |
| Reportes | Parcial | PDF y Excel básicos; faltan reportes avanzados. |
| Tests | Parcial | Tests de feature del control de acceso y del alta de usuarios; el resto sin cubrir. |

## Próximos pasos

1. Corregir los problemas de [docs/problemas-conocidos.md](docs/problemas-conocidos.md), empezando por los de seguridad.
2. Aplicar un *scope* global por operación o empresa activa.
3. Completar el módulo de técnicos: historial por técnico y validación de consumos por el despachante.
4. Reportes avanzados (stock crítico, movimientos por período, consumo por técnico).
5. Tests unitarios y de integración, y CI.
