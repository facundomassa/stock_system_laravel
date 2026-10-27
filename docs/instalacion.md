# Instalación y configuración

## Requisitos

| Componente | Versión | Notas |
|------------|---------|-------|
| PHP | ≥ 8.1 (la imagen Docker usa 8.2) | Extensiones: `pdo_mysql`, `mbstring`, `xml`/`dom`, `gd`, `zip`, `bcmath`, `exif`, `pcntl`. `gd` y `zip` las usan DomPDF y Laravel Excel. |
| Composer | 2.x | |
| MySQL / MariaDB | MySQL 8 o MariaDB 10.x | Las migraciones usan claves foráneas, así que el motor tiene que ser InnoDB. |
| Node.js + npm | Node 16 o superior | Solo para compilar los assets con Vite 3. |

Framework: **Laravel 9** (`laravel/framework ^9.19`).

## Instalación local

```bash
git clone https://github.com/facundomassa/stock_system_laravel.git
cd stock_system_laravel

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Editar `.env` con los datos de la base (ver [variables de entorno](#variables-de-entorno)) y crear la base de datos vacía. Después:

```bash
php artisan migrate --seed   # crea las tablas, roles, permisos, operaciones y usuarios de prueba
npm run dev                  # Vite en modo desarrollo (o `npm run build` para generar public/build)
php artisan serve            # http://127.0.0.1:8000
```

> `npm run dev` tiene que quedar corriendo mientras se usa la aplicación en desarrollo, porque las vistas cargan los assets con la directiva `@vite`. Si no se quiere tener Vite corriendo, usar `npm run build` una vez.

## Docker

El `docker-compose.yml` levanta tres contenedores en la red `stock-net`:

| Servicio | Contenedor | Imagen | Puerto host | Función |
|----------|------------|--------|-------------|---------|
| `app` | `stock-app` | `.docker/php/Dockerfile` (php:8.2-fpm + Composer) | — | Ejecuta PHP-FPM en el puerto 9000 interno. |
| `nginx` | `stock-nginx` | `nginx:alpine` | `8000` → 80 | Sirve `public/` y pasa los `.php` a `app:9000` (`.docker/nginx/default.conf`). |
| `db` | `stock-db` | `mysql:8.0` | `33060` → 3306 | Base `stock_system_laravel`, usuario `root`, contraseña `secret`. Los datos persisten en el volumen `dbdata`. |

Pasos:

1. En `.env`, apuntar la base al contenedor:
   ```dotenv
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=stock_system_laravel
   DB_USERNAME=root
   DB_PASSWORD=secret
   ```
2. Levantar los contenedores:
   ```bash
   docker compose up -d --build
   ```
3. Instalar dependencias y preparar la base dentro del contenedor `app`:
   ```bash
   docker compose exec app composer install --no-interaction
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```
4. Compilar los assets **en el host** (la imagen no trae Node):
   ```bash
   npm install
   npm run build
   ```
5. Abrir http://localhost:8000. Nginx atiende directamente, así que no hace falta `php artisan serve`.

Por qué hace falta el paso 3 aunque el Dockerfile ya corre `composer install`: el servicio `app` monta el proyecto del host sobre `/var/www`, lo que tapa el `vendor/` que se generó durante el build de la imagen.

Para conectarse a MySQL desde el host: `127.0.0.1:33060`, usuario `root`, contraseña `secret`.

## Firebase Studio / Project IDX

La carpeta `.idx/` contiene la configuración del entorno de Firebase Studio (`dev.nix`): instala PHP 8.3, Composer, Node 20 y MySQL 8, ejecuta `composer install`, `npm install`, copia `.env`, genera la clave y crea la base `laravel_db`. La vista previa web corre `php artisan serve`. Si se usa este entorno, poner `DB_DATABASE=laravel_db` en `.env` y correr las migraciones a mano.

## Variables de entorno

Variables que importan para este proyecto (el resto son las estándar de Laravel):

| Variable | Valor de ejemplo | Uso |
|----------|------------------|-----|
| `APP_NAME` | `Sistema de Stock` | Título de la aplicación. |
| `APP_ENV` / `APP_DEBUG` | `local` / `true` | En producción: `production` / `false`. |
| `APP_URL` | `http://localhost` | URL base. |
| `DB_*` | — | Conexión MySQL. |
| `QUEUE_CONNECTION` | `sync` | El listener de alertas de stock (`SendStockAlertNotification`) y la notificación implementan `ShouldQueue`. Con `sync` se ejecutan en el mismo request. Si se cambia a `database` o `redis`, hay que correr un worker (`php artisan queue:work`). Para `database` también hay que crear la tabla de jobs (`php artisan queue:table && php artisan migrate`), porque no hay migración para ella. |
| `BROADCAST_DRIVER` | `log` | No se usa broadcasting real. |
| `MAIL_*` | — | Las notificaciones solo usan el canal `database`; el mail no es necesario. |
| `JWT_SECRET` | — | Solo hace falta para los endpoints JWT de la API (`/api/login`, `/api/user`). Se genera con `php artisan jwt:secret`. |

Configuración fija en `config/app.php`: zona horaria `America/Argentina/Buenos_Aires` y locale `es`.

## Usuarios de prueba

`php artisan db:seed` (o `migrate --seed`) ejecuta `database/seeders/DatabaseSeeder.php`, que crea:

- **Permisos:** `POP36`, `POP20`, `ADMIN`.
- **Operaciones:** `POP36`, `POP20`, `ADMIN` (con el mismo nombre que los permisos; ver [Roles y permisos](roles-y-permisos.md)).
- **Roles:** `admin` (con el permiso `ADMIN`), `dispatcher`, `tecnico`.
- **Usuarios:**

| Email | Contraseña | Rol | Permiso directo |
|-------|------------|-----|-----------------|
| `admin@admin.com` | `admin` | admin | — (hereda `ADMIN` del rol) |
| `dispatcher36@dispatcher.com` | `dispatcher36` | dispatcher | `POP36` |
| `dispatcher20@dispatcher.com` | `dispatcher20` | dispatcher | `POP20` |
| `tecnico@tecnico.com` | `tecnico` | tecnico | `POP36` |

El seeder es idempotente (`firstOrCreate`), así que se puede volver a ejecutar.

> **Importante:** son credenciales triviales y públicas. No ejecutar el seeder en producción, o cambiar las contraseñas inmediatamente.

El seeder **no** crea centros de stock, direcciones ni artículos. Para operar hace falta, como mínimo:

1. Crear una dirección (los centros de stock la requieren).
2. Crear los centros de stock de cada operación.
3. Cargar artículos (a mano o por [importación CSV](flujos-de-negocio.md#importación-masiva-csv)).
4. Para el técnico de prueba: asignarle un centro de stock de tipo *Móvil* desde **Administración → Usuarios** y configurar en la operación `POP36` el almacén de origen de solicitudes y el destino de consumo (ver [Portal del técnico](flujos-de-negocio.md#portal-del-técnico)).

## Compilación de assets

`vite.config.js` declara estos puntos de entrada:

- `resources/sass/app.scss`
- `resources/js/app.js`
- `resources/js/ajax/state.js` (carga de provincias según país)
- `resources/js/ajax/generatepdf.js`
- `resources/js/functions/movement.js` (pantalla de carga de artículos en un remito)

```bash
npm run dev    # servidor de desarrollo con recarga automática
npm run build  # genera public/build para producción
```

Además, las vistas cargan desde CDN: jQuery 3.5.1, Chart.js, Boxicons y la fuente Nunito. La aplicación necesita salida a internet desde el navegador para verse correctamente.

## Tests

```bash
php artisan test
```

`phpunit.xml` fuerza una base SQLite en memoria (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) y define un `APP_KEY` solo para tests, así que no hace falta MySQL ni `.env` para correrlos. Requiere la extensión `pdo_sqlite`.

| Test | Qué cubre |
|------|-----------|
| `tests/Feature/ReferCancelStockTest.php` | Emitir y cancelar un remito deja el stock del origen y del destino como estaba; cancelar desde un proveedor no toca el stock; finalizar acredita el destino. |
| `tests/Feature/MovementEditionTest.php` | Los artículos de un remito emitido no se pueden abrir, guardar ni borrar; en un remito ingresado sí. |
| `tests/Feature/OperationAccessTest.php` | El registro público está cerrado (`/register` y `POST /api/register`); `/operation-select` rechaza operaciones no permitidas; un usuario sin permisos, o al que le quitaron el de su operación, no entra a las pantallas de trabajo; el técnico entra sin operación; un usuario inactivo no entra. |
| `tests/Feature/AdminUserTest.php` | El admin crea usuarios desde el panel (rol, permisos, activo, centro), el alta se valida y un usuario que no es admin recibe 403. |

Los tests de remitos arman sus datos con el trait `tests/Concerns/CreatesStockScenario.php`.

> No correr los tests con la configuración cacheada (`php artisan config:cache`): Laravel ignora `phpunit.xml`, usa la base del `.env` y `RefreshDatabase` la vacía. Si hace falta, ejecutar antes `php artisan config:clear`.

## Despliegue en producción

Lista mínima:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan view:cache
```

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` generado.
- El usuario del servidor web tiene que poder escribir en `storage/` y `bootstrap/cache/`.
- El document root tiene que ser `public/`. El `.htaccess` de la raíz reescribe todo a `public/` para hostings compartidos con Apache donde no se puede cambiar el document root.
- Si se usa una cola distinta de `sync`, configurar un worker supervisado.
- Revisar [Problemas conocidos](problemas-conocidos.md), en especial la sección de seguridad, antes de exponer el sistema.
