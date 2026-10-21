# Roles y permisos

La autorización usa [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) v6, sin *teams*, con el guard `web`. Hay dos conceptos separados:

- **Roles**: definen *qué tipo de usuario* es (administrador, despachante, técnico).
- **Permisos**: definen *a qué operaciones* tiene acceso. Cada permiso se llama igual que una operación.

## Roles

| Rol | Para quién | Efecto en el código |
|-----|------------|---------------------|
| `admin` | Administrador del sistema | Acceso a las rutas protegidas con `role:admin`: usuarios, operaciones e importación CSV. Tiene el permiso `ADMIN`, así que al entrar se le selecciona la operación `ADMIN`, y con ella aparece el menú de administración. |
| `dispatcher` | Despachante / pañolero | Ningún chequeo del código usa este rol en particular: un despachante tiene acceso a todas las pantallas de trabajo y ve los datos según los permisos de operación que tenga. |
| `tecnico` | Técnico de campo | Entra directo al portal técnico, no necesita elegir operación y ve un menú reducido. Acceso exclusivo a las rutas `technical/*` (`role:tecnico`). |

Un usuario tiene un solo rol: la pantalla de edición de usuarios usa `syncRoles([$rol])`.

## Permisos = operaciones

- Cada **operación** (`operations.name`, por ejemplo `POP36`) tiene un **permiso** con el mismo nombre. Al crear una operación desde **Administración → Operaciones**, el permiso se crea solo.
- El vínculo es **solo por nombre**: no hay clave foránea. Si se renombra una operación, el permiso no se renombra y el usuario pierde el acceso. Como el modelo `Operation` guarda el nombre en mayúsculas, el permiso también tiene que estar en mayúsculas.
- Los permisos se asignan directo al usuario (casillas en la edición de usuario) o a través del rol (el rol `admin` tiene `ADMIN`).
- `User::allowedOperations()` devuelve las operaciones cuyo nombre está entre los permisos efectivos del usuario (`getAllPermissions()`, directos más los del rol).

Los permisos también deciden **quién recibe las notificaciones de stock**: cuando cambia un stock, se notifica a todos los usuarios con el permiso de la operación del centro (ver [Flujos de negocio](flujos-de-negocio.md#alertas-y-notificaciones)).

## Selección de operación

Después del login, los usuarios que no son técnicos pasan por `/operation-select`:

- Si tienen **una** operación permitida, se selecciona sola.
- Si tienen varias, eligen una. La elección se guarda en `session('selected_operation')` y dura lo que dura la sesión.

El middleware `checkSelectedOperation` bloquea las pantallas de trabajo hasta que haya una operación seleccionada.

### Qué filtra la operación activa

El filtrado por operación **todavía es parcial** (falta un *scope* global):

| Pantalla | ¿Filtra por operación? |
|----------|------------------------|
| Listado de remitos (`/refer`) | Sí: muestra solo los remitos cuyo **destino** es un centro de la operación activa. |
| Nuevo remito: centros de origen y destino | No: el formulario ofrece todos los centros de stock. |
| Menú de administración | Se muestra solo si la operación activa es `ADMIN`. |
| Notificaciones | Indirectamente: cada usuario recibe las de sus operaciones. |
| Stock, artículos, centros de stock, personas, direcciones, movimientos, tránsito, dashboard, reportes | No: muestran los datos de todas las operaciones. |

## Matriz de acceso a rutas

| Grupo de rutas | Middleware | admin | dispatcher | tecnico |
|----------------|------------|:-----:|:----------:|:-------:|
| `/`, `/login`, `/register` | público | ✔ | ✔ | ✔ |
| `/operation-select` | `auth`, `checkIfUserIsActive` | ✔ | ✔ | ✔ |
| `/admin/users*`, `/operation*`, `/importar` | + `role:admin` | ✔ | ✘ | ✘ |
| `/technical/*` | + `checkSelectedOperation`, `role:tecnico` | ✘ | ✘ | ✔ |
| Resto: `/home`, `/stock*`, `/refer*`, `/movement*`, `/transit`, `/article*`, `/person*`, `/direction*`, `/stockcenter*`, `/notifications*`, reportes | + `checkSelectedOperation` | ✔ | ✔ | ✔ \* |

\* Al técnico se le ocultan estas opciones del menú, pero puede acceder a ellas escribiendo la URL. La única restricción real es la del middleware.

Las rutas no tienen *policies* ni chequeos por registro: cualquier usuario que llegue a una pantalla puede ver y modificar cualquier registro de ese módulo, de cualquier operación.

## Usuarios inactivos

`users.is_active = false` bloquea el acceso: el middleware `checkIfUserIsActive` cierra la sesión en el siguiente request y muestra *"Tu cuenta está inactiva."*. Se cambia desde la edición de usuario. No hay borrado de usuarios desde la interfaz: se desactivan.

## Gestión de usuarios

**Administración → Usuarios** (`/admin/users`, solo admin) lista los usuarios con sus roles y permisos. Desde **Editar** se cambian:

- nombre, apellido y email;
- contraseña (opcional, mínimo 8 caracteres; vacía la deja como está);
- rol (uno solo);
- permisos de operación (casillas);
- estado activo o inactivo;
- centro de stock asignado (para técnicos).

### Alta de usuarios

- El registro en `/register` está **abierto al público**. El usuario nuevo queda activo, sin rol y sin permisos, y un admin tiene que asignárselos desde la edición.
- El formulario **Crear usuario** del panel de administración envía a la ruta pública `register`, que solo acepta visitantes sin sesión, así que con un admin logueado no crea el usuario. Ver [problemas conocidos](problemas-conocidos.md).
- El reseteo de contraseña por email está deshabilitado (`Auth::routes(['reset' => false])`): un admin cambia la contraseña desde la edición.

## Tareas habituales

**Dar de alta una operación nueva**
1. Admin → Operaciones → Nueva: nombre (se guarda en mayúsculas), origen de solicitudes y destino de consumo (opcionales; se pueden completar después de crear los centros).
2. Crear los centros de stock de esa operación.
3. Editar la operación para configurar los centros del portal técnico.
4. Asignar el permiso a los usuarios que van a trabajar en ella.

**Dar de alta un técnico**
1. El técnico se registra en `/register` (o lo crea un admin con `php artisan tinker`).
2. Admin → Usuarios → Editar: rol `tecnico`, permiso de su operación, centro de stock Móvil asignado, activo.

**Cambios de permisos hechos directo en la base**
Spatie cachea los permisos por 24 horas. Si se modifican tablas de permisos por SQL, limpiar la caché:

```bash
php artisan permission:cache-reset
```
