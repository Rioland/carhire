# Deploying to Hostinger (cPanel / shared hosting)

Laravel expects the web server to serve the `public` folder only. On cPanel the
document root is `public_html`. Here is the arrangement that works reliably.

## Before you upload

Run this locally so the server doesn't have to:

```bash
composer install --optimize-autoloader --no-dev
```

Then zip the whole project **including** `vendor/`.

## 1. Choose your PHP version

In hPanel → **Advanced → PHP Configuration**, set PHP to **8.2 or 8.3**.
Enable these extensions if they aren't already: `bcmath`, `ctype`, `fileinfo`,
`json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `gd`.

## 2. Upload

Using File Manager or FTP:

1. Create a folder next to `public_html` called `carhire`.
2. Upload and extract the zip into `carhire`.
3. Move **the contents of** `carhire/public/` into `public_html/`
   (so `public_html/index.php`, `public_html/.htaccess`, `public_html/assets/` exist).
4. Delete the now-empty `carhire/public` folder.

Your layout should look like:

```
/home/uXXXX/
├── carhire/          ← the application (not web-accessible, which is correct)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
└── public_html/      ← document root
    ├── index.php
    ├── .htaccess
    └── assets/
```

## 3. Point index.php at the application

Edit `public_html/index.php` and change the two `__DIR__` paths:

```php
require __DIR__.'/../carhire/vendor/autoload.php';

$app = require_once __DIR__.'/../carhire/bootstrap/app.php';
```

(In a stock Laravel 11/12 `index.php` these read `__DIR__.'/../vendor/autoload.php'`
and `__DIR__.'/../bootstrap/app.php'`.)

## 4. Create the database

hPanel → **Databases → MySQL Databases**. Create a database and a user, and note
the full names — Hostinger prefixes them, e.g. `u123456_carhire`.

## 5. Configure .env

Create `carhire/.env` (copy `.env.example` if present):

```
APP_NAME="Your Company Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456_carhire
DB_USERNAME=u123456_admin
DB_PASSWORD=your_db_password

SESSION_DRIVER=file
CACHE_STORE=file
LOG_CHANNEL=stack
LOG_LEVEL=error

ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@yourdomain.com
ADMIN_PASSWORD=a-strong-password
```

`APP_DEBUG=false` matters — leaving it `true` in production shows your database
credentials on any error page.

## 6. Run the setup commands

hPanel → **Advanced → SSH Access** (enable it), then:

```bash
cd ~/carhire
php artisan key:generate
php artisan migrate --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**No SSH on your plan?** Use hPanel → **Advanced → Cron Jobs** to run a one-off
command, or temporarily add this to a file at `public_html/setup.php`:

```php
<?php
require __DIR__.'/../carhire/vendor/autoload.php';
$app = require_once __DIR__.'/../carhire/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('key:generate');
$kernel->call('migrate', ['--seed' => true, '--force' => true]);
echo 'Done. DELETE THIS FILE NOW.';
```

Visit `https://yourdomain.com/setup.php` once, then **delete the file immediately**.

## 7. Uploaded images

`php artisan storage:link` creates a symlink. Shared hosting sometimes blocks
symlinks, and the link needs to point into `public_html` rather than `public`.

Run this once instead (SSH, or the same one-off file approach):

```bash
ln -s ~/carhire/storage/app/public ~/public_html/storage
```

If symlinks are disabled entirely, add this to `carhire/config/filesystems.php`
under `links`:

```php
'links' => [
    base_path('../public_html/storage') => storage_path('app/public'),
],
```

then run `php artisan storage:link`.

Test it by uploading a vehicle photo in the dashboard. If the image is broken, the
link isn't right.

## 8. Permissions

```bash
chmod -R 775 ~/carhire/storage ~/carhire/bootstrap/cache
```

## 9. Force HTTPS

Hostinger issues a free SSL certificate under **Security → SSL**. Once it's active,
turn on "Force HTTPS", or add this to the top of `public_html/.htaccess`:

```apache
RewriteEngine On
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 10. After any future code change

```bash
php artisan config:clear && php artisan config:cache
php artisan route:clear && php artisan route:cache
php artisan view:clear && php artisan view:cache
```

---

## Troubleshooting

**500 error, blank page** — set `APP_DEBUG=true` briefly and reload, or read
`carhire/storage/logs/laravel.log`. Set it back to `false` afterwards.

**"No application encryption key"** — run `php artisan key:generate`.

**Every page except the homepage 404s** — `.htaccess` didn't upload (it's hidden;
turn on "show hidden files" in File Manager) or `AllowOverride` is off.

**Images upload but don't display** — the `storage` link in step 7 is missing.

**Dashboard changes don't show on the site** — run `php artisan cache:clear`. The
settings are cached; saving through the dashboard clears them automatically, but a
manual database edit won't.

---

## A note on Vercel

You mentioned Vercel as an option. Vercel is built for serverless JavaScript, and
running Laravel there needs a PHP runtime shim, external MySQL, and S3 for uploads
because the filesystem is read-only. For this site, cPanel is the simpler and
cheaper choice. If you later want more performance, a small VPS with Nginx is the
natural next step, not Vercel.
