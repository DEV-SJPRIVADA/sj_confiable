# UAT desde cero (`uat.sjconfiable.com`)

Guía para montar un **entorno UAT nuevo** (Hostinger u hospedaje similar: PHP 8.2+, MySQL, panel web). Ajusta rutas si tu proveedor usa otros nombres.

## Cero riesgo para lo que ya funciona (producción)

El UAT debe ser **otro sitio**, no un “cambio” encima del actual. Si seguís estas reglas, **no tocáis** el sistema que ya está en línea:

| Qué | Producción (no tocar salvo despliegue acordado) | UAT (solo esto para ensayar) |
|-----|--------------------------------------------------|------------------------------|
| URL | Dominio principal / carpeta que ya usan los usuarios | Solo **subdominio** nuevo, p. ej. `uat.sjconfiable.com` |
| Carpeta en el servidor | La carpeta/document root que ya tiene el sitio vivo | **Otra carpeta** exclusiva para UAT (nunca sobrescribir la de prod al subir ZIP o Git) |
| Base de datos | BD que usa el sitio en producción | **BD nueva** solo para UAT; usuario MySQL con permisos **solo** sobre esa BD |
| `.env` | El `.env` del sitio en producción | **Archivo aparte** solo en la carpeta del UAT (`APP_URL`, `DB_*`, `APP_KEY` propios) |
| DNS | Registros del dominio principal si ya sirven al público | Solo **añadir** el registro del subdominio `uat` (A/CNAME); no borrar los registros que apuntan a producción |
| Migraciones / seed | Solo en prod cuando el equipo decida desplegar | Corré `migrate` / `db:seed` **solo** contra la BD UAT |

**Errores típicos que sí pueden romper o mezclar entornos:** apuntar `uat` al mismo document root que prod; reutilizar la misma `DB_DATABASE` que prod; editar el `.env` del sitio vivo pensando que es UAT; ejecutar `migrate:fresh` en la BD equivocada.

Antes de borrar algo “viejo” del servidor, confirmá que **no** es la carpeta ni la BD del sitio en producción.

## 1. Decisiones previas

| Decisión | Recomendación UAT |
|----------|-------------------|
| Dominio | Subdominio dedicado, p. ej. `https://uat.sjconfiable.com` |
| Base de datos | **BD y usuario MySQL solo para UAT** (no compartir con producción) |
| Datos | Semillas de desarrollo o dump **anonimizado**; nunca restaurar backup real de producción sin proceso legal/DPD |
| Código | Misma rama que ensayáis (p. ej. `main` o `develop`) |

## 2. DNS y sitio en el panel de hosting

1. Entrá al **hPanel** (o panel del hosting donde delegáis `sjconfiable.com`).
2. **Dominios → Subdominios** (o equivalente): creá `uat` → carpeta/document root propios (p. ej. `public_html/uat` o el directorio que asigne el panel).
3. Si el DNS del dominio **no** está en ese mismo panel, en la **zona DNS** del dominio añadí:
   - **A** `uat` → IP del servidor que indique el hosting, **o**
   - **CNAME** `uat` → el hostname que os den (según proveedor).

Esperá propagación DNS (minutos a horas). Verificá con `ping uat.sjconfiable.com` o herramientas online.

## 3. Base de datos MySQL

En el panel: **Bases de datos → MySQL** (nombres típicos):

1. Creá una base nueva, p. ej. `uXXXXXX_sjconfiable_uat`.
2. Creá un usuario con contraseña fuerte y **asignalo solo a esa BD** con todos los privilegios.
3. Anotá **host** (muchas veces `localhost` en Hostinger; si no, el valor exacto que muestre el panel), **puerto** (3306), nombre BD, usuario y contraseña.

## 4. Subir el proyecto Laravel

Opciones habituales:

- **Git**: clonar el repo en una carpeta **fuera** del document root (p. ej. `~/apps/sj_confiable_uat`) y enlazar `public` al document root del subdominio, **o**
- **ZIP + FTP/File Manager**: subir el proyecto completo y configurar el dominio para que el **document root** sea la carpeta `public` del proyecto.

Regla importante: la URL pública debe resolver solo sobre **`public/`** (donde está `index.php`), no la raíz del repo.

### Si el panel no permite cambiar document root a `public/`

Usá estas rutas típicas en Hostinger:

- Subir el contenido de la carpeta **`public`** del proyecto al document root del subdominio.
- Subir el resto del proyecto a una carpeta **por encima** (p. ej. `sj_confiable` junto a `public_html`) y editar `index.php` del front para que las rutas `require` apunten al `vendor` y `bootstrap` correctos (patrón estándar Laravel en hosting compartido).

Si tenés **SSH**, es más limpio dejar el repo íntegro y enlazar `public` como raíz web.

## 5. Variables de entorno (`.env` en el servidor)

Creá `.env` en la **raíz del proyecto** (no dentro de `public`). Ejemplo base para UAT:

```env
APP_NAME="SJ Confiable UAT"
APP_ENV=staging
APP_KEY=
APP_DEBUG=true
APP_URL=https://uat.sjconfiable.com

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=uXXXXXX_sjconfiable_uat
DB_USERNAME=uXXXXXX_usuario
DB_PASSWORD=contraseña_segura

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

FILESYSTEM_DISK=local

MAIL_MAILER=log
```

Luego en el servidor (SSH o terminal del hosting):

```bash
php artisan key:generate
```

Para **APP_DEBUG**: dejad `true` solo mientras depuráis; antes de considerar UAT “estable”, pasad a `false` y `LOG_LEVEL=warning` o similar.

**Semillas:** en UAT podéis usar contraseñas de prueba; no activéis `RUN_LEGACY_SQL_IMPORT` salvo que sepáis lo que importáis. Revisá `.env.example` y ajustá `SEED_*` solo si usáis `db:seed` en este entorno.

## 6. Instalación PHP y migraciones

Desde la **raíz del proyecto** (donde está `artisan`):

```bash
composer install --no-dev --optimize-autoloader
```

Si el hosting no trae Composer global, usad `composer.phar` descargado o el instalador del panel.

```bash
php artisan migrate --force
```

**BD con tablas ya creadas** (import o seed previo): `migrate --force` puede fallar en `create_legacy_sj_catalog_tables` (*Table already exists*). No borra datos. Aplicar al menos las extensiones:

```bash
php artisan migrate --force --path=database/migrations/2026_04_24_220000_add_canal_audience_to_respuesta_solicitudes.php
php artisan migrate --force --path=database/migrations/2026_04_25_130000_add_visible_para_cliente_to_documentos_tables.php
```

Sin la columna `canal`, el panel proveedor devuelve **500** al enviar o abrir respuesta.

**Usuario admin en UAT:** `db:seed` global solo corre con `APP_ENV=local`. Usar:

```bash
php artisan db:seed --class=Database\\Seeders\\LegacyCatalogSeeder --force
php artisan db:seed --class=Database\\Seeders\\LegacyIdentitySeeder --force
php artisan tinker --execute="Illuminate\Support\Facades\DB::table('t_usuarios')->where('usuario','Administrador')->update(['password'=>Illuminate\Support\Facades\Hash::make('SuClaveUAT'),'activo'=>1]);"
```

Opcional (datos demo): `LegacyOperationalDataSeeder` con `--class=... --force`.

Ajustá antes `SEED_ADMIN_PASSWORD`, `SEED_LEGACY_OPERATIONAL`, etc. en `.env` **solo en local**; en hosting: `SEED_DEV_PASSWORDS=false`, `RUN_LEGACY_SQL_IMPORT=false`.

## 7. Permisos y enlace de almacenamiento

```bash
chmod -R ug+rwx storage bootstrap/cache
```

### Enlace `public/storage` → `storage/app/public`

Los PDF subidos por proveedor/consultor se guardan en `storage/app/public/uploads/`. La URL pública es `/storage/uploads/...`.

**Hostinger:** `php artisan storage:link` suele fallar con *Call to undefined function exec()* (PHP deshabilita `exec`). Enlace manual por SSH:

```bash
cd /home/usuario/domains/ejemplo.com/public_html/uat
# Si public/storage es una carpeta copiada (no symlink), renombrarla:
mv public/storage public/storage.bak
ln -s ../storage/app/public public/storage
ls -la public/storage
```

Debe mostrar: `storage -> ../storage/app/public`

Si `public/storage` es una **carpeta duplicada** desactualizada, los PDF nuevos dan **403** o no cargan aunque existan en `storage/app/public/uploads/`.

**Alternativa** si `ln -s` no está permitido:

```bash
mkdir -p public/storage
cp -a storage/app/public/. public/storage/
chmod -R 755 public/storage
```

(Repetir `cp` tras cada subida de PDF, o resolver el symlink.)

### `.env` crítico para PDFs y correos

```env
APP_URL=https://uat.sjconfiable.com
LEGACY_DOCUMENTS_ROOT=/home/usuario/.../uat/storage/app/public/uploads
```

No usar `APP_URL=http://172.16.x.x:8080` ni rutas Windows en hosting Linux.

## 8. HTTPS

Activá **SSL gratis** (Let’s Encrypt) en el panel para `uat.sjconfiable.com` y forzá `APP_URL` con `https://`.

## 9. Verificación rápida

- Abrí `https://uat.sjconfiable.com` → debe cargar login o home sin error 500.
- Si 500: revisá `storage/logs/laravel.log` y que `APP_KEY` exista.
- Probad login con un usuario sembrado o creado manualmente en BD.

## 10. Despliegues posteriores

1. `git pull` (o subir ZIP) en la carpeta del proyecto.
2. `composer install --no-dev --optimize-autoloader`
3. `php artisan migrate --force`
4. `php artisan config:cache` y `php artisan route:cache` cuando `APP_DEBUG=false`

---

## Checklist mínimo

- [ ] **Confirmado:** carpeta y BD UAT son **distintas** de las de producción (nombres anotados).
- [ ] Subdominio creado y DNS resolviendo al servidor correcto (sin borrar registros de prod).
- [ ] Document root = `public` de Laravel **solo en la carpeta UAT** (o equivalente).
- [ ] BD MySQL dedicada UAT y credenciales **solo** en el `.env` del UAT.
- [ ] `APP_KEY` generado; `APP_URL` con https apuntando al subdominio UAT.
- [ ] `composer install` + migraciones ejecutadas **en el proyecto UAT** contra la BD UAT (incl. `canal` si la BD ya tenía tablas).
- [ ] `public/storage` es **symlink** a `storage/app/public` (en Hostinger: `ln -s`, no `php artisan storage:link` si falla `exec`).
- [ ] `APP_URL` = URL pública HTTPS del subdominio; `LEGACY_DOCUMENTS_ROOT` = ruta Linux en el servidor.
- [ ] Usuario admin de prueba creado (seeders por `--class` + contraseña en tinker).
- [ ] SSL activo para el subdominio.

Si más adelante automatizáis despliegues, podéis añadir un script en CI/CD que ejecute estos pasos sobre el servidor UAT por SSH.
