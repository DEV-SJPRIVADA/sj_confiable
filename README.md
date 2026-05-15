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

- Ejecutar migraciones: `php artisan migrate` (esquema alineado al legado SJ)
- En **local** (`APP_ENV=local`), `php artisan db:seed` ejecuta `LegacyCatalogSeeder`, `LegacyIdentitySeeder` (datos mínimos de prueba) y, si aplica, `LocalDevPasswordSeeder` (`SEED_DEV_PASSWORDS`, `SEED_ADMIN_PASSWORD`)
- Opcional: con `RUN_LEGACY_SQL_IMPORT=true`, import único desde `database/archive/bootstrap_legacy.sql` solo si aún no existe `t_usuarios` (mysql/mariadb local)

```bash
php artisan db:seed
```

**Importante:** no subir el archivo `.env` al repositorio. Para un entorno nuevo, parta de `.env.example` y defina claves reales o de desarrollo de forma local.

**UAT / servidor:** para levantar el subdominio de pruebas desde cero (DNS, BD, `public`, migraciones y checklist), ver [`docs/uat-deploy-from-scratch.md`](docs/uat-deploy-from-scratch.md).

#### Variables de entorno útiles (desarrollo)

| Variable | Descripción |
|----------|-------------|
| `RUN_LEGACY_SQL_IMPORT` | `true`/`false`: import opcional del volcado archivado (solo local, mysql/mariadb, sin `t_usuarios` previa; por defecto `false`) |
| `SEED_DEV_PASSWORDS` | Activa el seeder de contraseñas de prueba para ids de usuario definidos en el seeder |
| `SEED_ADMIN_PASSWORD` | Contraseña en texto plano (solo dev) usada para generar el hash con `Hash::make` en usuarios de prueba |
| `SEED_LEGACY_OPERATIONAL` | `true`/`false`: datos operativos demo (solicitudes, respuestas, notificaciones) vía `LegacyOperationalDataSeeder` (por defecto `true` en `.env.example`) |
| `LEGACY_DOCUMENTS_ROOT` | Ruta absoluta a la carpeta de PDFs del sistema anterior (importación / descargas) |
| `MAIL_*` | SMTP para avisos por correo (ver [Correo y notificaciones](#correo-y-notificaciones)) |
| `MAIL_NOTIFICATIONS_ENABLED` | `true`/`false`: envía correos en los mismos hitos que la campana del panel (por defecto `true`) |

#### Migraciones y semillas (sin import SQL obligatorio)

- **Esquema:** las migraciones `2026_04_24_*` crean tablas de framework (sesiones, caché, colas) y el **esquema legado SJ** en orden de dependencias (`t_cat_*`, `solicitudes`, `notificaciones`, `t_solicitudes_usuario`, etc.).
- **Semillas locales** (`APP_ENV=local`, `php artisan db:seed`):
  - `LegacyCatalogSeeder`: roles, catálogo de servicios, paquetes (incl. id 22 de referencia).
  - `LegacyIdentitySeeder`: clientes, personas, proveedor y usuarios de prueba (ids coherentes con el dump archivado).
  - `LegacyOperationalDataSeeder` (si `SEED_LEGACY_OPERATIONAL=true`): solicitudes, respuestas, documentos de respuesta, notificaciones y solicitud de usuario; paridad con `database/archive/bootstrap_legacy.sql` para desarrollo.
  - `LocalDevPasswordSeeder` (si `SEED_DEV_PASSWORDS=true`): unifica contraseñas según `SEED_ADMIN_PASSWORD`.
- **Opcional:** `RUN_LEGACY_SQL_IMPORT=true` ejecuta **una sola vez** el import de `database/archive/bootstrap_legacy.sql` (solo si no existe `t_usuarios`; MySQL/MariaDB local). El flujo recomendado es `migrate` + `db:seed`.

## Autenticación y roles

El login usa la tabla `t_usuarios` (modelo `App\Models\Usuario`). Los roles siguen `t_cat_roles` (`id_rol`), alineados con el enum `App\Domain\Enums\UserRole`.

Resumen de paneles:

- **Consultor / admin SJ** (roles 2, 3): prefijo ` /panel/consultor` — inicio (dashboard), mi perfil, usuarios, clientes, asociados, confiabilidad, solicitudes de gestión de usuarios (`t_solicitudes_usuario`), informes, detalle (**respuesta SJ frente al cliente**, asignación a asociado), modal de notificaciones.
- **Cliente** (1, 4, 5): ` /panel/cliente` — inicio con filtros y gráficos (Chart.js), listado de solicitudes, **vista Estado de solicitud**, alta/edición de solicitud, anulación y perfil; **campana de notificaciones** (`notificaciones_cliente` por usuario); iconos de acciones según política.
- **Proveedor** (6): ` /panel/proveedor` — inicio orientativo, solicitudes asignadas (detalle, respuesta con PDF y cambio de estado vía `ProveedorSolicitudRespuestaService`; notificaciones a consultores SJ), perfil y **modal de notificaciones** (`notificaciones_proveedor`; deep link opcional desde campana).

### Panel consultor: catálogos y permisos

Rutas bajo `panel/consultor` con `authorize` y políticas registradas en `AppServiceProvider`:

| Área | Rutas (resumen) | Política |
|------|------------------|----------|
| Clientes | listado (orden, búsqueda, paginación); **crear/editar en modales**; activar/inactivar con switch (cliente y usuarios vinculados) | `ClientePolicy` |
| Asociados (proveedores) | listado (orden, búsqueda, paginación); **crear/editar en modales**; eliminación sujeta a reglas de negocio en servicio | `ProveedorPolicy` |
| Usuarios | listado (orden, búsqueda, paginación); **crear y editar en modales** sobre el listado; reglas de roles alineadas al legado (admin 2 no edita superadmin 3 ni asigna roles SJ sin permisos) | `UsuarioPolicy` |
| Solicitudes de usuarios | listado, responder aprobar/rechazar con comentario | `SolicitudUsuarioPolicy` |
| Informes (solicitudes) | listado con filtros y paginación; exportación CSV (simil Excel) | `SolicitudPolicy` |
| Mi perfil | datos personales (`t_persona`), modal *Actualizar datos* | (usuario autenticado) |
| Notificaciones (panel) | modal desde campana: lista **sólo no leídas** en `notificaciones` por `rol_destino (2/3)`; `POST …/marcar-leidas`; distintivo de campana = **sólo** notificaciones sin leer (no suma solicitudes usuario pendientes ni estado “Nuevo”) | (usuario rol 2/3) |
| Solicitudes de confiabilidad | gestión (listado), detalle: **respuesta** (`POST …/respuesta`) + asignación a proveedor | `SolicitudPolicy` (`manageAsConsultor`, `assignToProveedor`) |

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

- **Listado** `GET /panel/consultor/solicitudes`: pantalla *Gestión de Solicitudes* alineada al legado: conmutador Activas/Inactivas (`solicitudes.activo`; inactivas en gris `table-secondary`), búsqueda, ordenación, paginación, **cuatro colores de fila por estado** (paridad `sj_confiable1`: Nuevo/Registrado gris azulado, En proceso amarillo, Completado verde, Cancelado salmón; clases `fila-nuevo`, `fila-en-proceso`, `fila-completado`, `fila-cancelado` vía `LegacySolicitudFilaEstado`), acciones con icono PDF (`public/images/pdf.png`) y contador de documentos (sin documentos el control no navega). La **lupa** abre un **modal** (*Detalle de Solicitud*) sin salir del listado; la **lista** enlaza a la vista de gestión con ancla `#historial` (abre el panel de historial).
- **Datos:** `SolicitudRepository::paginateForConsultor()` y `baseListQuery()` cargan relaciones necesarias (incl. `paquete`, `proveedorAsignado`, `documentos` donde aplica).
- **Vista por solicitud** `GET /panel/consultor/solicitudes/{solicitud}`: cabecera con **Volver**, **Detalle** e **Historial**; **offcanvas** detalle/documentos (`#documentos`) e historial (`#historial`); cuerpo central con ***Nueva respuesta*** — formulario activo (`POST /panel/consultor/solicitudes/{solicitud}/respuesta`, multipart): texto obligatorio, hasta 10 PDF, nuevo estado; historial con canal `cliente_sj` si el mensaje es **visible al cliente**, o `solo_sj` si el consultor marca solo trámite interno (sin aviso ni línea en el historial del panel cliente). Notificación a la organización cliente vía `SolicitudNotificacionService` solo en envíos visibles al cliente. Bloque ***Asignación de asociado de negocio*** (`POST …/asignar`): selector de asociado, cliente final/tipo opcionales y **mensaje opcional para el asociado** (`comentario_asignacion`, máx. 2000 caracteres); el texto queda en `respuesta_solicitudes` (canal `sj_proveedor`) y en la notificación/correo al proveedor; **no** dispara aviso al cliente. Fragmentos compartidos: `_fragment-documentos-*`, `_fragment-historial-respuestas` + `_fragment-historial-chat` (historial tipo chat; audiencia por `canal`).
- **Adjuntos y visibilidad en panel cliente:** en *Nueva respuesta*, los checks *Adjuntos ya en expediente — incluir en el aviso al cliente* envían referencias `doc-{id}` / `dresp-{id}` (`ResponderSolicitudConsultorRequest::refsAdjuntosNotificacion`). Al guardar un envío **visible al cliente**, `ConsultorSolicitudRespuestaService` sincroniza `documentos.visible_para_cliente` y `documentos_respuesta.visible_para_cliente`: solo permanecen visibles para el cliente los marcados, las subidas del cliente (`cargado_desde_panel_cliente`) y los PDF nuevos adjuntados en ese envío. Los PDF que sube el asociado quedan ocultos al cliente hasta que el consultor los incluya así. El listado/descarga cliente usa esos flags (`EloquentSolicitudRepository` con canal cliente, `SolicitudArchivoController`).
- **Documentos operativos (consultor):** eliminación controlada de PDF de solicitud o de respuesta madre vía `SolicitudDocumentoController` y rutas dedicadas (reglas en política/servicio).
- **Partials consultor:** `_modal-detalle-solicitud.blade.php` (modal listado), `_offcanvases-gestion-solicitud.blade.php` (paneles laterales en show).

#### Informes de solicitudes (consultor)

- **Rutas:** `GET /panel/consultor/informes` (listado) y `GET /panel/consultor/informes/exportar` (descarga de archivo CSV UTF-8 con BOM, separador `;`, nombre `informe_solicitudes_YYYY-mm-dd_HHMMSS.csv`). Los filtros se pasan por query string en ambas (`desde`, `hasta`, `estado`, `servicio_id`, `per_page`).
- **Filtros:** rango de fechas sobre `fecha_creacion`, estado, servicio (incluye solicitudes cuyo servicio venga de `solicitudes.servicio_id` o de la tabla pivot `solicitud_servicios` vía relación `serviciosPivote`).
- **Vista** `resources/views/panel/consultor/informes/index.blade.php`: título *Informes de Solicitudes*, acciones *Filtrar* y *Exportar a Excel*, tabla con columnas Cliente, Documento, Nombres, Fecha creación, Estado (badges) y Servicio, estilo cebra y pie con rango de resultados mostrado.
- **Código:** `app/Http/Controllers/Panel/Consultor/InformesController.php` (`index`, `export`, `buildInformesQuery`).

#### Mi perfil (consultor)

- **Rutas:** `GET /panel/consultor/perfil`, `PUT /panel/consultor/perfil` (datos de `t_persona` vinculada al usuario).
- **Código:** `PerfilController`, `UpdatePerfilPropioRequest`, `PerfilPropioService`, vista `resources/views/panel/consultor/perfil/show.blade.php` (tarjeta y modal, vista previa de foto en cliente; sin persistencia de imagen en BD en esta fase).
- En el menú de usuario, enlace *Mi Perfil*; el nombre en barra superior usa datos de persona cuando existen.

#### Notificaciones (consultor / cliente / proveedor)

- **Consultor (`notificaciones`, roles destino 2 y 3):** `POST /panel/consultor/notificaciones/marcar-leidas` — marca leídas por ID o todas las del rol. Listado modal: **solo no leídas**; mismo criterio que el contador de la campana. Bandeja **compartida por rol** (todos los consultores con rol 2 o 3 ven el mismo aviso).
- **Cliente (`notificaciones_cliente`, usuarios 1/4/5):** campana en `navbar-cliente`; `POST /panel/cliente/notificaciones/marcar-leidas`; `NotificacionClienteService`; modal `modal-notificaciones-cliente.blade.php`; enlace “ver estado” a `solicitudes.estado`. Bandeja **por usuario** (`id_usuario_destino`).
- **Proveedor (`notificaciones_proveedor`, rol 6):** campana en `navbar-proveedor`; `POST /panel/proveedor/notificaciones/marcar-leidas`; `NotificacionProveedorService`; modal `modal-notificaciones-proveedor.blade.php`.
- **Disparadores de negocio** (`SolicitudNotificacionService` + `SolicitudCorreoNotificacionService`):

| Evento | Panel | Correo (si `MAIL_NOTIFICATIONS_ENABLED=true`) |
|--------|--------|-----------------------------------------------|
| Cliente **crea** solicitud | `notificaciones` roles 2 y 3 | Consultores SJ (correos en `t_persona`, roles 2/3) |
| Cliente crea solicitud | **No** avisa al cliente creador | **No** |
| Cliente **edita** solicitud | `notificaciones` roles 2 y 3 + fila en historial (`cliente_sj`, incluye comentarios si los hay) | Consultores SJ (mensaje con comentario si aplica) |
| Cliente **cancela** solicitud | `notificaciones` roles 2 y 3 + historial | Consultores SJ; si había asociado asignado, también `notificaciones_proveedor` |
| Consultor responde **visible al cliente** | `notificaciones_cliente` (usuarios activos del `id_cliente`) | Mismos destinatarios |
| Consultor responde solo trámite interno (`solo_sj`) | **No** cliente | **No** |
| Consultor **asigna** asociado | `notificaciones_proveedor` | `correo_proveedor` + usuarios del proveedor (incluye comentario opcional) |
| Asignación | **No** cliente | **No** |
| Proveedor responde | `notificaciones` roles 2 y 3 | Consultores SJ |

- Modelos: `Notificacion`, `NotificacionCliente`, `NotificacionProveedor`. `Notificacion` usa `scopeNoLeidas` coherente con `leido` null/0.
- Plantilla de correo: `resources/views/mail/solicitud-aviso.blade.php` (`App\Mail\SolicitudAvisoMail`).

#### Correo y notificaciones

Configurar SMTP en `.env` (no versionar contraseñas). Ejemplo **Microsoft 365 / Outlook**:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=tu_cuenta@sjsp.com.co
MAIL_PASSWORD=contraseña_de_aplicacion
MAIL_FROM_ADDRESS=tu_cuenta@sjsp.com.co
MAIL_FROM_NAME="${APP_NAME}"
MAIL_NOTIFICATIONS_ENABLED=true
```

**Importante:** en Laravel 12 use `MAIL_SCHEME=smtp` (STARTTLS en puerto 587) o `smtps` (puerto 465). No use `tls` como valor de `MAIL_SCHEME`.

Tras cambiar `.env`: `php artisan config:clear`.

Prueba rápida: desde `php artisan tinker`, ejecutar `Mail::raw('Prueba', fn ($m) => $m->to('tu_correo@sjsp.com.co')->subject('Test'));` o disparar una solicitud de prueba y revisar bandeja y log.

Los fallos SMTP se registran en `storage/logs/laravel.log` y **no** interrumpen el guardado de la solicitud (avisos en `app()->terminating()` tras crear/editar/cancelar/asignar cuando aplica).

#### Inicio (dashboard) consultor

- Ruta: `GET /panel/consultor/inicio` — datos y gráficos respetan **filtros GET** (empresa/cliente, servicio, estado, rango de fechas). La vista no muestra título “Inicio” ni bloque de accesos rápidos al final; entra con filtros, KPI, gráficos y tablas recientes.
- **Cuatro gráficos** (Chart.js vía CDN): dona *Solicitudes por estado*, barras agrupadas *Solicitudes por servicio* (paquetes vs servicios individuales), dona *Solicitudes por empresa*, evolución mensual (dos series: paquetes vs individuales). Debajo, **dos tablas** (*Solicitudes recientes* y *Peticiones de usuarios*).
- Lógica de agregación: `app/Services/Panel/ConsultorDashboardService.php`.
- Barra superior: logo en header `public/images/Logo Sj Confiable-02.png` (navbars autenticados), icono de notificaciones con distintivo (solo no leídas en bandeja). Pie de página corporativo en el layout del consultor.

#### Tipografía (panel, CSS legado)

- `plantilla.css`: `body` con ligero incremento de tamaño heredado; navbar y desplegables con tamaños en `rem` (p. ej. enlaces de menú ~1.1rem).
- `panel-tables-laravel.css` y `tablas-optimizadas.css`: celdas de tablas y cabeceras con tamaños ligeramente mayores que la escala mínima original (~0.875rem / ~0.9375rem) para legibilidad.

#### Panel cliente — solicitudes e inicio

| Ruta (resumen) | Descripción |
|-----------------|-------------|
| `GET /panel/cliente/inicio` | Dashboard cliente: filtros (estado, fechas, búsqueda), gráficos (estados, servicios, ciudades, evolución) y tabla *Solicitudes de confiabilidad*. |
| `GET /panel/cliente/solicitudes` | Listado tipo legado (`listaEstilo cliente-legacy`); columna **Acciones** con enlace a estado, detalle, `#documentos`, editar y anular según política. |
| `GET /panel/cliente/solicitudes/{id}/estado` | **Estado de solicitud** (primer icono portapapeles ✓): detalle izquierdo, documentos relacionados (`id="documentos"`), **historial tipo chat** (`_fragment-historial-chat`, modo cliente). |
| `GET /panel/cliente/solicitudes/{id}` | Detalle compacto (`_detalle` / fragmentos documentos + historial). |
| `GET/POST` crear y `PUT` editar solicitud | Formularios alineados al legado; edición si solicitud activa y no Completada/Cancelada (`openClienteEdit` / `update` en `SolicitudPolicy`); al guardar se registra historial y aviso a consultores SJ. |
| `POST …/cancelar` | Anula solicitud (`Cancelado`, `activo = 0`) con **modal** de confirmación (`_modal-anular-solicitud-cliente`); política `cancel`. |
| `POST …/notificaciones/marcar-leidas` | Marca leídas notificaciones cliente (modal campana). |
| Adjuntos a solicitud | `POST …/solicitudes/{id}/archivos/documento` (PDF, política y permisos subida documentos); descargas sujetas a `visible_para_cliente`. |

- **Partial compartido:** `resources/views/panel/partials/_cliente-acciones-solicitud-inner.blade.php` y estilos `_styles-cliente-acciones-solicitud.blade.php` (Inicio + listado Solicitudes).
- **Código:** `ClienteInicioController`, `ClienteInicioService`, `ClienteSolicitudController`, `ClienteSolicitudCreacionService`, `ClienteSolicitudActualizacionService`, `ClienteSolicitudCancelacionService`, `ClienteSolicitudDocumentoAdjuntoService`, `NotificacionClienteService`, `ImportacionMasivaController`, `PerfilController`, `Cliente\NotificacionController`; requests `StoreClienteSolicitudRequest`, `UpdateClienteSolicitudRequest`, `MarcarNotificacionesClienteRequest`.
- **Repositorio:** `SolicitudRepository::findForDetalle`, `findForEstadoCliente`.

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

### Historial de respuestas y canal (`respuesta_solicitudes.canal`)

- Enum `HistorialRespuestaCanal`: `cliente_sj` (frente al cliente y consultores SJ), `sj_proveedor` (operación con asociado; el repositorio no expone estas filas en la vista cliente) y `solo_sj` (trámite interno SJ, sin presencia en el historial del panel cliente).
- **UI tipo chat** (consultor, cliente estado, proveedor offcanvas): partial `resources/views/panel/solicitudes/_fragment-historial-chat.blade.php`, estilos `public/css/legacy/historial-chat.css`. Cada registro muestra usuario y fecha/hora, cambio de estado (si hubo) y texto de `respuesta`; colores por canal; orden cronológico ascendente. Si el texto incluye bloque `Comentario:` (creación/edición cliente, asignación consultor), la vista lo separa en un subbloque compacto (sin duplicar saltos de línea).
- **Comentarios en historial:** al crear o editar solicitud, el campo `comentarios` del formulario se anexa al texto del historial (`\n\nComentario:\n…`) y, en edición, también al correo/aviso a consultores SJ (`solicitudEditadaPorCliente`).
- Wrapper de tarjeta: `_fragment-historial-respuestas.blade.php` (incluye el chat en detalle consultor y offcanvas).
- Inserciones compatibles con BD sin columna `canal`: `App\Support\RespuestaSolicitudHistorial`. El repositorio omite filtros por `canal` / `visible_para_cliente` si esas columnas aún no existen (útil tras despliegue sin migrar).
- Migraciones de backfill opcionales para filas sin canal, comentarios de creación en historial y reclasificación de historial consultor.

## Estructura relevante (código)

- `app/Http/Controllers/Panel/Consultor/`: controladores del panel consultor (incl. `PerfilController`, `NotificacionConsultorController`, `InformesController`, `SolicitudController` con `respuesta` y `asignar`, `SolicitudDocumentoController` para borrado de adjuntos)
- `app/Http/Requests/Catalog/`: validación de catálogos (consultor); en **usuarios**, **clientes** y **asociados**, los *Form requests* de alta/edición usan `failedValidation` para volver al listado y reabrir el modal correspondiente cuando hay errores.
- `app/Models/`: modelos Eloquent mapeando tablas legado (`solicitudes`, `t_usuarios`, `t_clientes`, `t_proveedores`, `notificaciones`, etc.)
- `app/Policies/`: `SolicitudPolicy`, `ClientePolicy`, `ProveedorPolicy`, `UsuarioPolicy`, `SolicitudUsuarioPolicy`, y `Policies/Concerns/AuthorizesSJStaff`
- `app/Services/Solicitud/`: `SolicitudAsignacionService`, `ClienteSolicitudCreacionService`, `ClienteSolicitudActualizacionService`, `ClienteSolicitudCancelacionService`, `ConsultorSolicitudRespuestaService`, `ProveedorSolicitudRespuestaService`, `ClienteSolicitudDocumentoAdjuntoService`, `SolicitudNotificacionService`, `SolicitudCorreoNotificacionService`, documentos y rutas de almacenamiento (`SolicitudDocumentoPathResolver` si aplica)
- `app/Support/RespuestaSolicitudHistorial.php`: atributos de inserción en historial según columnas disponibles
- `app/Support/LegacySolicitudFilaEstado.php`: clases CSS de fila por estado (paridad legado)
- `config/notifications.php`: `MAIL_NOTIFICATIONS_ENABLED`
- `app/Repositories/Contracts/SolicitudRepository.php` y `EloquentSolicitudRepository.php`: listados y `paginateForConsultor` (búsqueda y orden)
- `resources/views/panel/consultor/solicitudes/`: index (gestión), show (gestión/respuesta con offcanvas), modales y partials asociados
- `resources/views/panel/consultor/informes/`: informe de solicitudes (listado y export CSV)
- `public/images/pdf.png`: icono de documentos en el listado (referencia al legado)
- `app/Services/Catalog/`: lógica de clientes, proveedores, usuarios y respuesta a solicitudes de usuario
- `app/Services/Panel/ConsultorDashboardService.php`: dashboard consultor (KPI, gráficos, tablas recientes)
- `app/Services/Panel/PerfilPropioService.php`, `NotificacionConsultorService`, `NotificacionClienteService`, `NotificacionProveedorService`
- `resources/views/panel/consultor/perfil/`, `layouts/partials/modal-notificaciones-consultor.blade.php`, `modal-notificaciones-cliente.blade.php`, `modal-notificaciones-proveedor.blade.php`
- `config/sj.php`: URLs opcionales (login: WhatsApp, redes, olvidé contraseña)
- `public/css/legacy/`: hojas de estilo copiadas/adaptadas del sistema anterior (`plantilla.css`, `historial-chat.css`, tablas, etc.)
- `resources/views/panel/solicitudes/_fragment-historial-chat.blade.php`, `_fragment-historial-respuestas.blade.php`
- `resources/views/panel/partials/_modal-anular-solicitud-cliente.blade.php`
- `database/archive/bootstrap_legacy.sql`: volcado de referencia (no sustituye un backup de producción; el flujo normal es migrate + seed).
- `database/migrations/2026_04_24_*.php` y posteriores: esquema Laravel + legado SJ; extensiones (`canal` / audiencia en historial, visibilidad de adjuntos al cliente `2026_04_25_130000_*`, backfills de historial).
- `resources/views/panel/cliente/`: inicio, solicitudes (index, create, edit, estado, show), importar, partials de acciones.

## Pruebas

```bash
php artisan test
```

## Licencia

El esqueleto Laravel se distribuye bajo [licencia MIT](https://opensource.org/licenses/MIT). Los términos del proyecto y los activos de marca (logos, identidad) corresponden a su titular.
