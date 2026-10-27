# Problemas conocidos y deuda técnica

Relevados leyendo el código mientras se escribía esta documentación (rama `master`, octubre 2026). Cada punto indica dónde está el problema y qué efecto tiene. Los que ya se corrigieron pasan a [Resueltos](#resueltos), con la referencia a la corrección y a sus tests.

Gravedad: 🔴 alta · 🟠 media · 🟡 baja.

## Seguridad

### 🟠 No hay autorización por operación ni por registro
Fuera de `role:admin` y `role:tecnico`, no hay *policies* ni filtros por operación (ver [Roles y permisos](roles-y-permisos.md#qué-filtra-la-operación-activa)). Un despachante de `POP20` puede ver, editar, emitir o cancelar remitos de `POP36` si conoce el ID, y un técnico puede abrir cualquier pantalla escribiendo la URL.

### 🟠 Emitir un remito es un GET
`GET /refer/emited/{refer}` (`routes/web.php:78`) mueve stock. Al no pasar por la verificación CSRF, un enlace o una imagen en otra página puede emitir remitos en nombre de un usuario logueado, y también lo puede disparar el *prefetch* del navegador. Debería ser `PATCH`/`POST` con token, como finalizar.

### 🟠 XSS en el buscador de artículos
`resources/js/functions/movement.js` arma las filas con *template strings* y `append` sin escapar (`${result[index].name}`, `code`, `type`). Un artículo con HTML o JavaScript en el nombre se ejecuta en el navegador de quien abra la carga de artículos de un remito.

### 🟡 Endpoint de artículos sin autenticación
`GET /api/article` (`routes/api.php:36`) devuelve el catálogo completo sin pedir sesión ni token.

### 🟡 Credenciales y archivos sensibles en el repositorio
- `DatabaseSeeder` crea usuarios con contraseñas triviales (`admin`/`admin`, etc.).
- En la raíz hay un archivo con un nombre accidental (`er-hasPermissionTo('ADMIN');`, con un carácter invisible), que es una salida de `tinker` con los datos del usuario admin, incluido el hash de su contraseña. También hay un `backup.sql` vacío. Conviene borrar ambos.

## Errores funcionales

### 🟠 El buscador de artículos falla al filtrar dentro de un remito
`ArticleService::addStockInfo` (`app/Services/ArticleService.php:69`) hace `Refer::with('originStockCenter')`, pero la relación se llama `origin`. Como `movement.js` siempre manda el parámetro `refer`, el filtro de la pantalla de carga de artículos devuelve error 500. Además, el JS lee `stock.quantity` cuando la API devuelve el número directo en `stock`.

### 🟠 El stock nunca baja de cero
`StockService::adjustStockcenter` (`app/Services/StockService.php:114-116`) corrige a 0 cualquier resultado negativo. Si se emite más de lo disponible, la diferencia se pierde sin aviso y el indicador de "stock negativo" del dashboard nunca se activa por uso normal. Conviene validar la disponibilidad al emitir o permitir negativos y alertar.

También afecta a la cancelación: al cancelar un remito emitido se devuelve al origen la cantidad completa de cada renglón, aunque al emitir se haya descontado menos por el piso en 0. Por ejemplo, con 3 unidades en el origen, emitir 5 deja el origen en 0 y cancelar lo deja en 5.

### 🟠 "Borrar todas" las notificaciones no funciona
`DELETE /notifications/clear-all` (`routes/web.php:108`) se declara después de `DELETE /notifications/{id}` (línea 106), que la captura con `id = "clear-all"`. El usuario ve *"No se pudo eliminar la notificación"*. Hay que declararla antes.

### 🟠 "Ver todas" las notificaciones da error 500
La ruta `notifications.index` (`routes/web.php:103`), enlazada desde `layouts/notificationbox.blade.php:102`, apunta a `NotificationController@index`, que no existe.

### 🟠 CUIT y códigos de artículo más largos que su columna
- `persons.cuit` es `INT` (máximo 2.147.483.647), pero un CUIT tiene 11 dígitos y la validación pide 10 u 11. Con MySQL en modo estricto (`config/database.php`, `'strict' => true`), guardar un CUIT real da error *Out of range*. El ejemplo de la pantalla de importación (`20123456789`) también falla. Conviene usar `string(11)` o `BIGINT`.
- `articles.code` es `varchar(12)`, pero `ArticleRequest` acepta hasta 16 caracteres: un código de 13 a 16 caracteres pasa la validación y falla al guardar.

### 🟡 Los filtros rápidos del stock no filtran
`StockController::index` (`app/Http/Controllers/StockController.php:78-92`) arma `$filters['negative']` y `$filters['alerts']` a partir de `?filter=`, pero no se los pasa a `paginateStocks`. Los enlaces "Ver detalles" y "Ver alertas" del dashboard muestran el stock sin filtrar.

### 🟡 La rama de administrador de los destinos permitidos nunca se cumple
`ReferController::getAllowedDestinations` (`app/Http/Controllers/ReferController.php:147`) compara la operación con `'admin'` en minúsculas, pero las operaciones se guardan en mayúsculas (`ADMIN`). En consecuencia, el admin ve los remitos cuyo destino es un centro de la operación `ADMIN`; si esa operación no tiene centros, no se filtra y ve todos.

### 🟡 Botón "Finalizar" en remitos ingresados
El listado y el detalle muestran "Finalizar" también en estado `I`, pero el servicio solo acepta `E` y devuelve un error.

### 🟡 Registro por API roto
`UserController` (`app/Http/Controllers/UserController.php:5`) importa `App\User`, que no existe (el modelo es `App\Models\User`), y no envía `surname`, que es obligatorio. `POST /api/register` responde 500. Si se arregla, volvería a abrir el alta pública que se cerró en la web (ver [Resueltos](#resueltos)), así que conviene borrar la ruta en lugar de arreglarla. Tampoco hay `config/jwt.php` publicado: hace falta `JWT_SECRET` para que funcione el login JWT.

### 🟡 Rutas declaradas sin método o tapadas
- `Route::resource('technical', ...)` (`routes/web.php:65`): `TechnicalController` no implementa ninguna acción REST.
- `POST /stock` → `StockController@store` no existe.
- `/stock/negative`, `/stock/dead`, `/stock/alerts` y `/refer/pending` las tapan `stock/{stock}` y `refer/{refer}` y responden error; ninguna vista las usa.

### 🟡 La marca de tránsito queda activa en remitos hacia consumo
Al finalizar un remito cuyo destino es de tipo `C` no se llama a `adjustStock`, así que sus movimientos quedan con `transit = true` para siempre. Hoy no se nota porque la vista de tránsito filtra por estado del remito y no por esa columna.

### 🟡 Cada cambio de stock genera notificaciones para toda la operación
`SendStockAlertNotification` avisa de **cualquier** variación, no solo cuando se cruza el nivel de alerta, a todos los usuarios con el permiso de la operación. Un remito de 30 artículos genera 30 notificaciones por usuario.

## Modelo de datos

- 🟠 **Borrados en cascada agresivos**: borrar una operación, dirección, persona, artículo o usuario elimina en cadena centros, stock, remitos y movimientos. Ver [Modelo de datos](modelo-de-datos.md#borrados-en-cascada). Conviene usar `restrict` o borrado lógico.
- 🟡 `stocks` no tiene índice único (`id_stockcenter`, `id_article`).
- 🟡 Columna `stocks.limit` sin uso.
- 🟡 Migración `2024_11_13_194735_create_operation_table` crea una tabla `operation` (singular) duplicada y sin uso.
- 🟡 `Movement` declara `quantity_origen` y `quantity_destiny` en `$fillable`, y los métodos `setQuantityOrigen()`/`setQuantityDestiny()`, pero esas columnas no existen.
- 🟡 Las longitudes de las validaciones no coinciden con las columnas (personas: `max:100` contra `varchar(60)`; observación del remito: `max:60` contra `varchar(255)`).

## Rendimiento

- 🟡 `layouts/movementbox.blade.php` carga **todos** los movimientos de remitos emitidos en cada página (`MovementService::getTransitMovements`, que pagina en memoria).
- 🟡 `StockcenterController::index` y `show` consultan operación, dirección y persona fila por fila (N+1).
- 🟡 `DashboardService::getDispatchEfficiency` trae todos los remitos finalizados para promediar en PHP.
- 🟡 `ReferService::getArticlesWithStockInfo` y `TechnicalController::dashboard` cargan el catálogo completo de artículos.

## Código sin uso o legado

| Elemento | Ubicación |
|----------|-----------|
| `PermissionController` (vacío, sin rutas) | `app/Http/Controllers/PermissionController.php` |
| `Notificationalert` y `App\Notifications\NotificationController` | `app/Notifications/` |
| `StockHelper` | `app/Helpers/StockHelper.php` |
| `ReferStatusData` (DTO) | `app/DTOs/ReferStatusData.php` |
| `MovementRequest` (Form Request) | `app/Http/Requests/MovementRequest.php` |
| `RetiredArticles` y el paquete `consoletvs/charts` | `app/Charts/` |
| `StockService::getStockSummary` (usa `Article->cost`, que no existe) | `app/Services/StockService.php` |
| `DashboardService::getStockSnapshot` | `app/Services/DashboardService.php` |
| Barra de navegación anterior | `resources/views/layouts/navbar.blade.php` |
| Plantillas HTML de reportes | `resources/views/exports/` |
| `Cors` (modifica el request, no la respuesta, y no está asignado a ninguna ruta) | `app/Http/Middleware/Cors.php` |
| `generatepdf.js` con la URL `http://stocksystem.com` fija | `resources/js/ajax/generatepdf.js` |
| `x_fecha_español()` usa `strftime`, deprecado desde PHP 8.1 | `app/Helpers/helpers.php` |
| Variable `$tittle` (con doble t) en el layout, que ninguna vista define | `resources/views/layouts/app.blade.php` |

## Calidad y herramientas

- Pocos tests: solo cubren emitir, cancelar y finalizar remitos y la edición de sus artículos (`tests/Feature/ReferCancelStockTest.php`, `tests/Feature/MovementEditionTest.php`), el control de acceso (`tests/Feature/OperationAccessTest.php`) y el alta de usuarios desde el panel (`tests/Feature/AdminUserTest.php`). El resto del sistema no tiene tests.
- Sin CI: el workflow `php.yml` que existía se borró.
- `tymon/jwt-auth` está fijado en `dev-develop` y `minimum-stability` es `dev`: las instalaciones no son reproducibles fuera del `composer.lock`.
- La imagen Docker (`.docker/php/Dockerfile`) usa `php:8.2-fpm`, pero el `composer.lock` pide PHP 8.3 o 8.4 (`lcobucci/clock` 3.5.0 exige ≥ 8.3), así que `composer install` falla dentro del contenedor. `composer.json` sigue declarando `^8.1`.
- Conviven dos estilos de controlador (con servicios y sin ellos); los ABM viejos validan en el controlador y usan `request()->except(...)` para asignación masiva.

## Resueltos

### Cualquier persona podía registrarse y operar sobre todos los datos
✅ Corregido en octubre de 2026. Antes, `/register` estaba abierto al público, el usuario nuevo quedaba activo y `HomeController::operationSelect` guardaba en la sesión **cualquier** valor de `selected_operation`: con un POST a `/operation-select` se entraba a todas las pantallas de trabajo (stock, remitos, centros, personas…) y se las podía modificar.

- `routes/web.php` declara `Auth::routes(['register' => false, 'reset' => false])`, así que `/register` responde 404. Si se vuelve a habilitar, `RegisterController::create` crea al usuario inactivo (`is_active = false`) hasta que un admin lo active.
- `HomeController::operationSelect` valida `selected_operation` contra `get_allowed_operations_list()`. Una operación para la que el usuario no tiene permiso se rechaza con un error de validación y no se guarda en la sesión.
- `CheckSelectedOperation` vuelve a comprobar en cada request que la operación de la sesión siga permitida. Si no lo está (por ejemplo, porque un admin le quitó el permiso al usuario), la descarta y redirige a `/operation-select`.
- Tests: `tests/Feature/OperationAccessTest.php`.

### Crear usuario desde el panel de administración no funcionaba
✅ Corregido en octubre de 2026. Antes, el formulario `admin/user/create.blade.php` enviaba a `route('register')`, que tiene el middleware `guest`: con un admin logueado, redirigía sin crear el usuario.

- Ahora envía a `POST /admin/users` (`AdminController@store`, solo admin), que guarda rol, permisos, estado activo y centro de stock con las mismas reglas que la edición.
- Tests: `tests/Feature/AdminUserTest.php`.

### Cancelar un remito emitido acreditaba el destino en vez de devolver al origen
✅ Corregido en octubre de 2026. Antes, `ReferService::cancelRefer` llamaba a `increaseStock`, que acredita siempre el centro **destino**: al cancelar un remito emitido, el origen no recuperaba la mercadería y el destino la recibía como si hubiera llegado.

- Ahora `cancelRefer` llama a `StockService::restoreOriginStock`, que suma las cantidades al centro **origen** y saca los movimientos de tránsito. El destino no se toca.
- Si el origen es un proveedor (`P`) no se modifica el stock, igual que al emitir.
- Tests: `tests/Feature/ReferCancelStockTest.php` (emitir y cancelar deja origen y destino como estaban; cancelar desde un proveedor no toca el stock; finalizar sigue acreditando el destino).
- **Datos anteriores a la corrección:** cada remito cancelado desde `E` con un origen que no fuera proveedor dejó acreditado el destino y sin reponer el origen. El sistema no guarda desde qué estado se canceló un remito, así que esos casos hay que revisarlos a mano (por ejemplo, con las notificaciones de cambio de stock de esas fechas).

### Se podían editar los artículos de un remito emitido
✅ Corregido en octubre de 2026. Antes, "Editar Articulos" aparecía en estado `I` **y** `E`, y `MovementController::store` no validaba el estado: si se cambiaba una cantidad después de emitir, el origen ya estaba descontado con la cantidad vieja y el destino se acreditaba con la nueva.

- El botón solo se muestra en estado `I` (`refer/show.blade.php` y `movement/show.blade.php`).
- `MovementController::create` y `store` rechazan los remitos que no están en `I`: vuelven al detalle del remito con el error *"No se pueden modificar los artículos del remito N porque está EMITIDO. Solo se pueden editar los artículos de remitos en estado INGRESADO."*
- Tests: `tests/Feature/MovementEditionTest.php`.
