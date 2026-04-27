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
| Clientes | listado, crear/editar, activar-inactivar (cliente y usuarios vinculados) | `ClientePolicy` |
| Asociados (proveedores) | CRUD; eliminación sujeta a reglas de negocio en servicio | `ProveedorPolicy` |
| Usuarios | listado, crear/editar; reglas de roles alineadas al legado (admin 2 no edita superadmin 3 ni asigna roles SJ sin permisos) | `UsuarioPolicy` |
| Solicitudes de usuarios | listado, responder aprobar/rechazar con comentario | `SolicitudUsuarioPolicy` |
| Solicitudes de confiabilidad | (existente) | `SolicitudPolicy` |

- **Form requests:** `app/Http/Requests/Catalog/` (alta/edición y respuesta de solicitudes de usuario).
- **Servicios:** `app/Services/Catalog/` (transacciones y validaciones de catálogo; `SolicitudUsuarioRespuestaService` para cerrar solicitudes).
- Vistas de formulario en `resources/views/panel/consultor/{clientes,asociados,usuarios}/` (create, edit) e índices con acciones según `@can`.

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
- `app/Http/Requests/Catalog/`: validación de formularios de catálogos (consultor)
- `app/Models/`: modelos Eloquent mapeando tablas legado (`solicitudes`, `t_usuarios`, `t_clientes`, `t_proveedores`, etc.)
- `app/Policies/`: `SolicitudPolicy`, `ClientePolicy`, `ProveedorPolicy`, `UsuarioPolicy`, `SolicitudUsuarioPolicy`, y `Policies/Concerns/AuthorizesSJStaff`
- `app/Services/Solicitud/`: lógica de listados y asignación a asociado
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
