# Problemas conocidos y deuda técnica

Relevados leyendo el código mientras se escribía esta documentación (rama `main`, octubre 2026). Cada punto indica dónde está el problema y qué efecto tiene. Ninguno se corrigió en este cambio: esta página solo los registra.

Gravedad: 🔴 alta · 🟠 media · 🟡 baja.

## Seguridad

### 🔴 Cualquier persona puede registrarse y operar sobre todos los datos
- `routes/web.php:35` habilita `/register` para cualquiera.
- `HomeController::operationSelect` (`app/Http/Controllers/HomeController.php:105-112`) guarda en la sesión **cualquier** valor que llegue en `selected_operation`, sin verificar que esté entre las operaciones permitidas.
- Efecto: alguien sin cuenta puede registrarse, mandar un POST a `/operation-select` con cualquier valor y entrar a todas las pantallas de trabajo (stock, remitos, centros, personas…), con permiso para modificarlas.
- Sugerencia: cerrar el registro público (`'register' => false`) o dejar a los usuarios nuevos inactivos hasta que un admin los habilite, y validar `selected_operation` contra `get_allowed_operations_list()`.

### 🟠 No hay autorización por operación ni por registro
Fuera de `role:admin` y `role:tecnico`, no hay *policies* ni filtros por operación (ver [Roles y permisos](roles-y-permisos.md#qué-filtra-la-operación-activa)). Un despachante de `POP20` puede ver, editar, emitir o cancelar remitos de `POP36` si conoce el ID, y un técnico puede abrir cualquier pantalla escribiendo la URL.

### 🟠 Emitir un remito es un GET
`GET /refer/emited/{refer}` (`routes/web.php:76`) mueve stock. Al no pasar por la verificación CSRF, un enlace o una imagen en otra página puede emitir remitos en nombre de un usuario logueado, y también lo puede disparar el *prefetch* del navegador. Debería ser `PATCH`/`POST` con token, como finalizar.

### 🟠 XSS en el buscador de artículos
`resources/js/functions/movement.js` arma las filas con *template strings* y `append` sin escapar (`${result[index].name}`, `code`, `type`). Un artículo con HTML o JavaScript en el nombre se ejecuta en el navegador de quien abra la carga de artículos de un remito.

### 🟡 Endpoint de artículos sin autenticación
`GET /api/article` (`routes/api.php:36`) devuelve el catálogo completo sin pedir sesión ni token.

### 🟡 Credenciales y archivos sensibles en el repositorio
- `DatabaseSeeder` crea usuarios con contraseñas triviales (`admin`/`admin`, etc.).
- En la raíz hay un archivo con un nombre accidental (`er-hasPermissionTo('ADMIN');`, con un carácter invisible), que es una salida de `tinker` con los datos del usuario admin, incluido el hash de su contraseña. También hay un `backup.sql` vacío. Conviene borrar ambos.

## Errores funcionales

### Cancelar un remito emitido acredita el destino en vez de devolver al origen
🔴 `ReferService::cancelRefer` (`app/Services/ReferService.php:128-133`) llama a `increaseStock`, que acredita siempre el centro **destino** (`StockService.php:81`). Al cancelar un remito emitido, el origen no recupera la mercadería y el destino la recibe como si hubiera llegado. Hace falta una variante de `adjustStock` que acredite el origen.

### Se pueden editar los artículos de un remito emitido
🔴 `refer/show.blade.php:87-88` muestra "Editar Artículos" en estado `I` **y** `E`, y `MovementController::store` no valida el estado. Si se cambia una cantidad después de emitir, el origen ya se descontó con la cantidad vieja y el destino se acredita con la nueva: el stock total cambia sin explicación. Debería bloquearse la edición fuera del estado `I`.

### 🟠 El buscador de artículos falla al filtrar dentro de un remito
`ArticleService::addStockInfo` (`app/Services/ArticleService.php:69`) hace `Refer::with('originStockCenter')`, pero la relación se llama `origin`. Como `movement.js` siempre manda el parámetro `refer`, el filtro de la pantalla de carga de artículos devuelve error 500. Además, el JS lee `stock.quantity` cuando la API devuelve el número directo en `stock`.

### 🟠 El stock nunca baja de cero
`StockService::adjustStock` (`app/Services/StockService.php:106-108`) corrige a 0 cualquier resultado negativo. Si se emite más de lo disponible, la diferencia se pierde sin aviso y el indicador de "stock negativo" del dashboard nunca se activa por uso normal. Conviene validar la disponibilidad al emitir o permitir negativos y alertar.

### 🟠 "Borrar todas" las notificaciones no funciona
`DELETE /notifications/clear-all` (`routes/web.php:106`) se declara después de `DELETE /notifications/{id}` (línea 104), que la captura con `id = "clear-all"`. El usuario ve *"No se pudo eliminar la notificación"*. Hay que declararla antes.

### 🟠 "Ver todas" las notificaciones da error 500
La ruta `notifications.index` (`routes/web.php:101`), enlazada desde `layouts/notificationbox.blade.php:102`, apunta a `NotificationController@index`, que no existe.

### 🟠 Crear usuario desde el panel de administración no funciona
`admin/user/create.blade.php:11` envía el formulario a `route('register')`, que tiene el middleware `guest`: con un admin logueado, redirige sin crear el usuario. Además `RegisterController` no guardaría rol, permisos ni centro de stock. Falta una ruta `POST admin/users` con su método `store`.

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
`UserController` (`app/Http/Controllers/UserController.php:5`) importa `App\User`, que no existe (el modelo es `App\Models\User`), y no envía `surname`, que es obligatorio. `POST /api/register` responde 500. Tampoco hay `config/jwt.php` publicado: hace falta `JWT_SECRET` para que funcione el login JWT.

### 🟡 Rutas declaradas sin método o tapadas
- `Route::resource('technical', ...)` (`routes/web.php:63`): `TechnicalController` no implementa ninguna acción REST.
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

- Sin tests propios: solo los de ejemplo de Laravel.
- Sin CI: el workflow `php.yml` que existía se borró.
- `tymon/jwt-auth` está fijado en `dev-develop` y `minimum-stability` es `dev`: las instalaciones no son reproducibles fuera del `composer.lock`.
- Conviven dos estilos de controlador (con servicios y sin ellos); los ABM viejos validan en el controlador y usan `request()->except(...)` para asignación masiva.
