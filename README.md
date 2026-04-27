# SJ Confiable (Laravel)

Aplicación web de **SJ Seguridad** para gestión de solicitudes de confiabilidad, mediación entre clientes y asociados de negocios, y catálogos de usuarios, clientes y proveedores. Proyecto en migración desde el sistema legado en PHP, conservando el esquema de base de datos y una interfaz alineada al look & feel corporativo.

- **Repositorio:** [github.com/DEV-SJPRIVADA/sj_confiable](https://github.com/DEV-SJPRIVADA/sj_confiable)
- **Framework:** Laravel 12
- **PHP:** 8.2+

## Requisitos

- PHP ≥ 8.2 con extensiones habituales de Laravel (openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, etc.)
- Composer
- MySQL 8+ o MariaDB (la base de producción se llama en convención `sj_confiable`)

## Puesta en marcha

```bash
git clone https://github.com/DEV-SJPRIVADA/sj_confiable.git
cd sj_confiable
composer install
cp .env.example .env
php artisan key:generate
```

Configurar en `.env` la conexión a MySQL (`DB_*`) y, si aplica, `APP_URL`.

### Base de datos (entorno local)

- Ejecutar migraciones si existen: `php artisan migrate`
- En **local** (`APP_ENV=local`), el `DatabaseSeeder` puede:
  - Importar `database/sql/bootstrap_legacy.sql` si no existe aún `t_usuarios` (controlado por `RUN_LEGACY_SQL_IMPORT`)
  - Unificar contraseñas de prueba con `LocalDevPasswordSeeder` (variables `SEED_DEV_PASSWORDS` y `SEED_ADMIN_PASSWORD`)

```bash
php artisan db:seed
```

**Importante:** no subir el archivo `.env` al repositorio. Para un entorno nuevo, parta de `.env.example` y defina claves reales o de desarrollo de forma local.

#### Variables de entorno útiles (desarrollo)

| Variable | Descripción |
|----------|-------------|
| `RUN_LEGACY_SQL_IMPORT` | `true`/`false`: importa el SQL de referencia al seedear (solo local, driver mysql/mariadb) |
| `SEED_DEV_PASSWORDS` | Activa el seeder de contraseñas de prueba para ids de usuario definidos en el seeder |
| `SEED_ADMIN_PASSWORD` | Contraseña en texto plano (solo dev) usada para generar el hash con `Hash::make` en usuarios de prueba |

## Autenticación y roles

El login usa la tabla `t_usuarios` (modelo `App\Models\Usuario`). Los roles siguen `t_cat_roles` (`id_rol`), alineados con el enum `App\Domain\Enums\UserRole`.

Resumen de paneles:

- **Consultor / admin SJ** (roles 2, 3): prefijo ` /panel/consultor` — inicio, usuarios, clientes, asociados, confiabilidad, solicitudes de gestión de usuarios (`t_solicitudes_usuario`), informes, detalle y asignación a proveedor donde aplique.
- **Cliente** (1, 4, 5): ` /panel/cliente`
- **Proveedor** (6): ` /panel/proveedor`

### Panel consultor: catálogos y permisos

Rutas bajo `panel/consultor` con `authorize` y políticas registradas en `AppServiceProvider`:

| Área | Rutas (resumen) | Política |
|------|------------------|----------|
| Clientes | listado (orden, búsqueda, paginación); **crear/editar en modales**; activar/inactivar con switch (cliente y usuarios vinculados) | `ClientePolicy` |
| Asociados (proveedores) | listado (orden, búsqueda, paginación); **crear/editar en modales**; eliminación sujeta a reglas de negocio en servicio | `ProveedorPolicy` |
| Usuarios | listado (orden, búsqueda, paginación); **crear y editar en modales** sobre el listado; reglas de roles alineadas al legado (admin 2 no edita superadmin 3 ni asigna roles SJ sin permisos) | `UsuarioPolicy` |
| Solicitudes de usuarios | listado, responder aprobar/rechazar con comentario | `SolicitudUsuarioPolicy` |
| Informes (solicitudes) | listado con filtros y paginación; exportación CSV (simil Excel) | `SolicitudPolicy` |
| Solicitudes de confiabilidad | gestión (listado), detalle/asignación | `SolicitudPolicy` |

- **Form requests:** `app/Http/Requests/Catalog/` (alta/edición y respuesta de solicitudes de usuario).
- **Servicios:** `app/Services/Catalog/` (transacciones y validaciones de catálogo; `SolicitudUsuarioRespuestaService` para cerrar solicitudes).
- Vistas de listado y **partials** de modales en `resources/views/panel/consultor/{clientes,asociados,usuarios}/` (p. ej. `partials/modals-*.blade.php`, `partials/form-body.blade.php`) e índices con acciones según `@can`.

#### Clientes (consultor) — modales y listado

- **Alta y edición** en **modales** desde el listado (`clientes/index`, `partials/modals-clientes`, `partials/form-body`). `GET .../clientes/crear` y `GET .../clientes/{id}/editar` redirigen con `open_modal` / `edit_cliente`.
- Listado alineado al legado: cabecera oscura, columnas NIT, razón social, dirección, ciudad, teléfono, correo, nombre, cargo, tipo; **Acciones**: cajita lápiz (dorada) + switch activo/inactivo.
- Campos tipo **cliente** y **ciudad** con desplegables desde catálogos en controlador; validación con `failedValidation` hacia el índice (`StoreClienteRequest`, `UpdateClienteRequest`).

#### Asociados de negocios (consultor) — modales y listado

- **Alta y edición** en **modales** (`asociados/index`, `partials/modals-asociados`, `partials/form-body`). `GET .../asociados/crear` y `GET .../asociados/{proveedor}/editar` redirigen con `open_modal` / `edit_proveedor`.
- Listado: NIT, razón social, comercial, ciudad, contacto, cargo; **Acciones**: cajita lápiz (editar) y cajita **papelera** (eliminar, con confirmación) si la política lo permite. Estilo de cabecera y pie de modal coherente con clientes/usuarios (degradado azul, icono de guardar en disquete).
- Validación: `StoreProveedorRequest` y `UpdateProveedorRequest` con `failedValidation` al listado; eliminación vía `ProveedorCatalogService` (bloquea si hay solicitudes o usuarios vinculados).

#### Usuarios (consultor) — modales y UX

- **Alta y edición** se realizan en **ventanas modales** desde el listado (`resources/views/panel/consultor/usuarios/index.blade.php` + `partials/modals-usuarios.blade.php` y `partials/form-body.blade.php`). Las rutas `GET .../usuarios/crear` y `GET .../usuarios/{id}/editar` **redirigen** al listado con `?open_modal=crear` o `?open_modal=editar&edit_usuario=...` para abrir el modal correspondiente.
- **Estilo** de cabecera y pie: degradado en azul oscuro (alineado al legado), botones Cerrar / Guardar (icono de guardado), permisos de documentos/solicitudes como **switches** (`form-switch`), orden de campos y checklist de contraseña en pantalla.
- **Validación** (`StoreUsuarioGestionRequest`, `UpdateUsuarioGestionRequest`): contraseña con reglas `Password` de Laravel (8–15 caracteres, mayúscula, minúscula, número, símbolo); identificación obligatoria. Si falla la validación, se redirige al listado con **reapertura** del modal y `withInput` (sin conservar la contraseña en el flujo de error).
- **Columna Acciones** del listado: botón de editar con **cajita** (borde dorado, icono de lápiz) y **switch** activo/inactivo alineados con flex; el botón de editar se atenúa visualmente si el usuario está inactivo.

#### Solicitudes de confiabilidad (consultor) — gestión, modal y vista de respuesta

- **Listado** `GET /panel/consultor/solicitudes`: pantalla *Gestión de Solicitudes* alineada al legado: conmutador Activas/Inactivas (`solicitudes.activo`), búsqueda, ordenación, paginación, filas resaltadas por estado, acciones con icono PDF (`public/images/pdf.png`) y contador de documentos (sin documentos el control no navega). La **lupa** abre un **modal** (*Detalle de Solicitud*) sin salir del listado; la **lista** enlaza a la vista de gestión con ancla `#historial` (abre el panel de historial).
- **Datos:** `SolicitudRepository::paginateForConsultor()` y `baseListQuery()` cargan relaciones necesarias (incl. `paquete`, `proveedorAsignado`, `documentos` donde aplica).
- **Vista por solicitud** `GET /panel/consultor/solicitudes/{solicitud}`: cabecera con **Volver al listado**, botones **Detalle** e **Historial**; **offcanvas** lateral izquierdo (detalle ampliado + bloque de documentos, hash `#documentos`) y derecho (historial de respuestas, hash `#historial`); cuerpo central con *Nueva respuesta* (formulario de envío en preparación) y *Asignación de asociado de negocio* (orden de campos alineado al legado). Fragmentos reutilizables: `resources/views/panel/solicitudes/_fragment-documentos-solicitud.blade.php` y `_fragment-historial-respuestas.blade.php` (compuestos desde `panel/solicitudes/_detalle.blade.php` para cliente y otros roles).
- **Partials consultor:** `_modal-detalle-solicitud.blade.php` (modal listado), `_offcanvases-gestion-solicitud.blade.php` (paneles laterales en show).

#### Informes de solicitudes (consultor)

- **Rutas:** `GET /panel/consultor/informes` (listado) y `GET /panel/consultor/informes/exportar` (descarga de archivo CSV UTF-8 con BOM, separador `;`, nombre `informe_solicitudes_YYYY-mm-dd_HHMMSS.csv`). Los filtros se pasan por query string en ambas (`desde`, `hasta`, `estado`, `servicio_id`, `per_page`).
- **Filtros:** rango de fechas sobre `fecha_creacion`, estado, servicio (incluye solicitudes cuyo servicio venga de `solicitudes.servicio_id` o de la tabla pivot `solicitud_servicios` vía relación `serviciosPivote`).
- **Vista** `resources/views/panel/consultor/informes/index.blade.php`: título *Informes de Solicitudes*, acciones *Filtrar* y *Exportar a Excel*, tabla con columnas Cliente, Documento, Nombres, Fecha creación, Estado (badges) y Servicio, estilo cebra y pie con rango de resultados mostrado.
- **Código:** `app/Http/Controllers/Panel/Consultor/InformesController.php` (`index`, `export`, `buildInformesQuery`).

#### Inicio (dashboard) consultor

- Ruta: `GET /panel/consultor/inicio` — datos y gráficos respetan **filtros GET** (empresa/cliente, servicio, estado, rango de fechas).
- **Cuatro gráficos** (Chart.js vía CDN): dona *Solicitudes por estado*, barras agrupadas *Solicitudes por servicio* (paquetes vs servicios individuales), dona *Solicitudes por empresa*, evolución mensual (dos series: paquetes vs individuales). Debajo, **dos tablas** (*Solicitudes recientes* y *Peticiones de usuarios*) y accesos rápidos a módulos.
- Lógica de agregación: `app/Services/Panel/ConsultorDashboardService.php`.
- Barra superior: icono de notificaciones con distintivo (solicitudes en estado *Nuevo* + solicitudes de usuario *Pendiente*). Pie de página corporativo en el layout del consultor.

### Acceso público y login

- La ruta `/` **no** usa la plantilla `welcome` de Laravel: redirige a `/login` (invitado) o al *home* del rol (`App\Domain\Routing\RoleHome`) si hay sesión.
- Pantalla de **login** en dos columnas (~70% vídeo / ~30% formulario en escritorio), logo `public/images/logo-sj-confiable.png` (parámetro `?v=mtime` para caché), contraseña con visibilidad, enlace a recuperar contraseña, botón *INGRESAR*, bloque *Síguenos* y flotante WhatsApp.
- Vídeo opcional: colocar `public/videos/login.mp4` en local (el MP4 no se versiona por defecto; ver `.gitignore`).

`config/sj.php` y variables opcionales en `.env` (ver `.env.example`):

| Variable | Uso |
|----------|-----|
| `SJ_WHATSAPP_URL` | Enlace `wa.me` del botón flotante |
| `SJ_FORGOT_PASSWORD_URL` | Página o flujo de recuperación de clave |
| `SJ_SOCIAL_FACEBOOK` / `LINKEDIN` / `INSTAGRAM` | Enlaces de redes en el login |

## Estructura relevante (código)

- `app/Http/Controllers/Panel/Consultor/`: controladores del panel consultor
- `app/Http/Requests/Catalog/`: validación de catálogos (consultor); en **usuarios**, **clientes** y **asociados**, los *Form requests* de alta/edición usan `failedValidation` para volver al listado y reabrir el modal correspondiente cuando hay errores.
- `app/Models/`: modelos Eloquent mapeando tablas legado (`solicitudes`, `t_usuarios`, `t_clientes`, `t_proveedores`, etc.)
- `app/Policies/`: `SolicitudPolicy`, `ClientePolicy`, `ProveedorPolicy`, `UsuarioPolicy`, `SolicitudUsuarioPolicy`, y `Policies/Concerns/AuthorizesSJStaff`
- `app/Services/Solicitud/`: lógica de listados y asignación a asociado
- `app/Repositories/Contracts/SolicitudRepository.php` y `EloquentSolicitudRepository.php`: listados y `paginateForConsultor` (búsqueda y orden)
- `resources/views/panel/consultor/solicitudes/`: index (gestión), show (gestión/respuesta con offcanvas), modales y partials asociados
- `resources/views/panel/consultor/informes/`: informe de solicitudes (listado y export CSV)
- `public/images/pdf.png`: icono de documentos en el listado (referencia al legado)
- `app/Services/Catalog/`: lógica de clientes, proveedores, usuarios y respuesta a solicitudes de usuario
- `app/Services/Panel/ConsultorDashboardService.php`: dashboard consultor (KPI, gráficos, tablas recientes)
- `config/sj.php`: URLs opcionales (login: WhatsApp, redes, olvidé contraseña)
- `public/css/legacy/`: hojas de estilo copiadas/adaptadas del sistema anterior (`plantilla.css`, tablas, etc.)
- `database/sql/bootstrap_legacy.sql`: volcado de referencia local (no sustituye un backup de producción)

## Pruebas

```bash
php artisan test
```

## Licencia

El esqueleto Laravel se distribuye bajo [licencia MIT](https://opensource.org/licenses/MIT). Los términos del proyecto y los activos de marca (logos, identidad) corresponden a su titular.
