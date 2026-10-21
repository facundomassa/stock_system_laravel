# Rutas

Definidas en `routes/web.php` y `routes/api.php`. Los middleware y sus efectos se explican en [Arquitectura](arquitectura.md#ciclo-de-un-request) y [Roles y permisos](roles-y-permisos.md#matriz-de-acceso-a-rutas).

Las rutas `Route::resource` generan las siete acciones REST estándar:

| Método | URI | Acción | Nombre |
|--------|-----|--------|--------|
| GET | `/{recurso}` | `index` | `{recurso}.index` |
| GET | `/{recurso}/create` | `create` | `{recurso}.create` |
| POST | `/{recurso}` | `store` | `{recurso}.store` |
| GET | `/{recurso}/{id}` | `show` | `{recurso}.show` |
| GET | `/{recurso}/{id}/edit` | `edit` | `{recurso}.edit` |
| PUT/PATCH | `/{recurso}/{id}` | `update` | `{recurso}.update` |
| DELETE | `/{recurso}/{id}` | `destroy` | `{recurso}.destroy` |

## Rutas web

### Públicas

| Método | URI | Controlador | Descripción |
|--------|-----|-------------|-------------|
| GET | `/` | closure | Página de bienvenida (`welcome`). |
| GET/POST | `/login` | `Auth\LoginController` | Login. Redirige a `/operation-select`, o a `/technical/dashboard` si es técnico. |
| POST | `/logout` | `Auth\LoginController` | Logout. |
| GET/POST | `/register` | `Auth\RegisterController` | Registro abierto (nombre, apellido, email, contraseña). |
| GET/POST | `/password/confirm` | `Auth\ConfirmPasswordController` | Confirmación de contraseña de Laravel UI. |

### `auth` + `checkIfUserIsActive`

| Método | URI | Controlador | Descripción |
|--------|-----|-------------|-------------|
| GET/POST | `/operation-select` | `HomeController@operationSelect` | Elegir la operación de trabajo. |

### `+ role:admin`

| Método | URI | Controlador | Nombre |
|--------|-----|-------------|--------|
| GET | `/admin/users` | `AdminController@index` | `admin.users.index` |
| GET | `/admin/users/create` | `AdminController@create` | `admin.users.create` |
| GET | `/admin/users/{user}/edit` | `AdminController@edit` | `admin.users.edit` |
| PUT | `/admin/users/{user}` | `AdminController@update` | `admin.users.update` |
| resource | `/operation` | `OperationController` | `operation.*` |
| GET | `/importar` | `ImportController@create` | `import.create` |
| POST | `/importar` | `ImportController@store` | `import.store` |

### `+ checkSelectedOperation` + `role:tecnico`

| Método | URI | Controlador | Nombre |
|--------|-----|-------------|--------|
| GET | `/technical/dashboard` | `TechnicalController@dashboard` | `technical.dashboard` |
| GET | `/technical/request` | `TechnicalController@showRequestForm` | `technical.request.form` |
| GET | `/technical/consume` | `TechnicalController@showConsumeForm` | `technical.consume.form` |
| resource | `/technical` | `TechnicalController` | `technical.*` (el controlador no implementa ninguna de estas acciones) |

### `+ checkSelectedOperation`

**General**

| Método | URI | Controlador | Nombre |
|--------|-----|-------------|--------|
| GET | `/home` | `HomeController@index` | `home` |
| GET | `/howtouse` | `HomeController@howtouse` | `howtouse` |
| GET | `/home/reportRpFyS` | `HomeController@reportRpFyS` | `home.reportRpFyS` |
| GET | `/home/reportAllMovement` | `HomeController@reportAllMovement` | `home.reportAllMovement` |

**Datos maestros**

| Método | URI | Controlador |
|--------|-----|-------------|
| resource | `/person` | `PersonController` |
| resource | `/article` | `ArticleController` |
| resource | `/direction` | `DirectionController` |
| resource | `/stockcenter` | `StockcenterController` |

**Remitos**

| Método | URI | Controlador | Descripción |
|--------|-----|-------------|-------------|
| resource | `/refer` | `ReferController` | ABM. `destroy` **cancela** el remito (no lo borra). |
| GET | `/refer/emited/{refer}` | `ReferController@emited` | Emitir (`I → E`). |
| PATCH | `/refer/finalized/{refer}` | `ReferController@finalized` | Finalizar (`E → F`). |
| GET | `/refer/get/pdf/{refer}` | `ReferController@getPdf` | PDF del remito. |

**Movimientos**

| Método | URI | Controlador | Descripción |
|--------|-----|-------------|-------------|
| GET | `/movement` | `MovementController@index` | Todos los movimientos, paginados. |
| GET | `/movement/create/{refer}` | `MovementController@create` | Carga de artículos del remito. |
| GET | `/movement/{refer}/edit` | `MovementController@create` | Alias de la anterior. |
| POST | `/movement` | `MovementController@store` | Guarda los cambios en lote. |
| GET | `/movement/show/{refer}` | `MovementController@show` (`movement.show`) | Artículos del remito, solo lectura. |
| GET | `/transit` | `MovementController@transit` | Remitos en tránsito. |

**Stock**

| Método | URI | Controlador | Nombre |
|--------|-----|-------------|--------|
| GET | `/stock` | `StockController@index` | `stock.index` |
| GET | `/stock/{stock}` | `StockController@show` | `stock.show` |
| PUT | `/stock/{stock}` | `StockController@update` | `stock.update` (solo cambia `quantity_alert`) |
| GET | `/stock/get/pdf` | `StockController@getPdf` | — |
| GET | `/stock/get/excel` | `StockController@getExcel` | — |
| POST | `/stock` | `StockController@store` | — (el método no existe) |

Filtros de `/stock` por *query string*: `stockselect` (ID de centro o `*`), `type`, `articlename`, `code`.

**Notificaciones**

| Método | URI | Controlador | Nombre |
|--------|-----|-------------|--------|
| GET | `/notifications` | `NotificationController@index` | `notifications.index` (el método no existe) |
| GET | `/notifications/count` | `NotificationController@count` | `notifications.count`. Devuelve `{unread_count, total_count}`. |
| POST | `/notifications/{id}/read` | `NotificationController@markAsRead` | `notifications.markAsRead` |
| DELETE | `/notifications/{id}` | `NotificationController@delete` | `notifications.delete` |
| POST | `/notifications/mark-all-read` | `NotificationController@markAllAsRead` | `notifications.markAllRead` |
| DELETE | `/notifications/clear-all` | `NotificationController@clearAll` | `notifications.clearAll` (la tapa la ruta anterior, ver [problemas conocidos](problemas-conocidos.md)) |

**Atajos del dashboard (sin uso)**

`/stock/negative`, `/stock/dead`, `/stock/alerts` y `/refer/pending` están declaradas, pero las tapan `stock/{stock}` y `refer/{refer}`, que se registran antes, y ninguna vista las usa. El dashboard usa `/stock?filter=...` y `/refer?status=E`.

## Rutas de la API (`/api`)

Grupo `api`: sin sesión, con throttle de 60 solicitudes por minuto.

| Método | URI | Auth | Controlador | Descripción |
|--------|-----|------|-------------|-------------|
| GET | `/api/country` | — | `api\ApiCountryController@country` | Países ordenados por nombre, desde `database/data/countries.json`. |
| GET | `/api/state/{country}` | — | `api\ApiCountryController@state` | Provincias del país (por nombre). `[]` si no existe. |
| GET | `/api/article` | — | `ArticleController@filters` | Busca artículos. Parámetros: `nameTx`, `typeTx`, `codeTx`, `refer` (agrega el stock del origen del remito). |
| POST | `/api/register` | — | `UserController@register` | Alta con JWT (no funciona; ver problemas conocidos). |
| POST | `/api/login` | — | `UserController@authenticate` | Devuelve `{token}` JWT. |
| GET | `/api/user` | `jwt.verify` | `UserController@getAuthenticatedUser` | Usuario del token. |
| GET | `/api/user` | `auth:sanctum` | closure | Misma URI que la anterior pero declarada antes; Laravel se queda con la última registrada (la de JWT), así que esta queda sin efecto. |

## Canales de broadcasting

`routes/channels.php` define el canal privado `App.Models.User.{id}` (el que trae Laravel por defecto). No se usa: `BROADCAST_DRIVER=log` y el front no tiene Echo configurado.
