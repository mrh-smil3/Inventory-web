# Dokumentasi Teknis — Konfigurasi Sistem

**Versi:** 1.0
**Terakhir Diperbarui:** 12 Juli 2026
**Target Pembaca:** Developer / Tim Teknis

---

## Daftar Isi

1. [Cara Kerja Konfigurasi di Laravel](#1-cara-kerja-konfigurasi-di-laravel)
2. [`.env` — Environment Variables](#2-env--environment-variables)
3. [`config/app.php`](#3-configappphp)
4. [`config/database.php`](#4-configdatabasephp)
5. [`config/auth.php`](#5-configauthphp)
6. [`config/session.php`](#6-configsessionphp)
7. [`config/cache.php`](#7-configcachephp)
8. [`config/queue.php`](#8-configqueuephp)
9. [`config/mail.php`](#9-configmailphp)
10. [`config/logging.php`](#10-configloggingphp)
11. [`config/filesystems.php`](#11-configfilesystemsphp)
12. [`config/services.php`](#12-configservicesphp)
13. [`config/permission.php`](#13-configpermissionphp)
14. [`config/filament-shield.php`](#14-configfilament-shieldphp)
15. [`AdminPanelProvider.php`](#15-adminpanelproviderphp)
16. [Panduan Deploy](#16-panduan-deploy)
17. [Troubleshooting](#17-troubleshooting)

---

## 1. Cara Kerja Konfigurasi di Laravel

Laravel menggunakan sistem konfigurasi **dua lapis**:

```
.env (nilai aktual)  →  config/*.php (membaca .env via env())
```

| Layer | Lokasi | Fungsi |
|-------|--------|--------|
| **`.env`** | Root project | Berisi nilai aktual (berbeda tiap environment) |
| **`config/*.php`** | Folder `config/` | Membaca nilai dari `.env` dengan fallback ke default |

**Aturan penting:**
- **`.env` JANGAN di-commit** ke repository (sudah ada di `.gitignore`)
- **`config/*.php` DI-COMMIT** ke repository (berisi default values & logic)
- Untuk mengubah config, edit `.env` — bukan `config/*.php`

**Cara mengakses config di kode:**
```php
// Dalam PHP
config('app.name')          // → "Toko Berkah Jaya"
config('database.default')  // → "mysql"

// Dalam Blade
{{ config('app.name') }}
```

**Cache config (opsional, untuk production):**
```bash
php artisan config:cache    # Cache semua config ke satu file
php artisan config:clear    # Hapus cache config
```

> Setelah `config:cache`, perubahan di `.env` tidak akan terbaca sampai cache di-clear.

---

## 2. `.env` — Environment Variables

Lokasi: `Project/Inventory-web/.env`

### 2.1 Status Aktual

| Variabel | Nilai Aktual | Keterangan |
|----------|-------------|------------|
| `APP_NAME` | `Toko Berkah Jaya` | Nama aplikasi |


### 2.2 Variabel yang Perlu Diubah Saat Deploy

| Variabel | Default | Yang Harus Diubah |
|----------|---------|-------------------|
| `APP_ENV` | `local` | → `production` |
| `APP_DEBUG` | `true` | → `false` |
| `APP_URL` | `http://localhost` | → URL production (misal: `https://stokku.my.id`) |
| `APP_KEY` | `base64:...` | **JANGAN di-share** — sudah benar, jangan di-regenerate kecuali perlu |
| `DB_HOST` | `127.0.0.1` | → Host database production |
| `DB_PORT` | `8889` | → Port database production (3306 untuk MySQL default) |
| `DB_DATABASE` | `inventory_web` | → Nama database production |
| `DB_USERNAME` | `root` | → Username database production |
| `DB_PASSWORD` | `root` | → Password database production |
| `MAIL_MAILER` | `log` | → `smtp` / `ses` / dll (untuk kirim email asli) |
| `MAIL_HOST` | `127.0.0.1` | → SMTP host production |
| `MAIL_PORT` | `2525` | → SMTP port production (587/465) |
| `MAIL_USERNAME` | `null` | → SMTP username |
| `MAIL_PASSWORD` | `null` | → SMTP password |

---

## 3. `config/app.php`

Lokasi: `config/app.php`

Konfigurasi umum aplikasi Laravel.

### 3.1 Setting Aktif

| Key | Nilai | Sumber | Keterangan |
|-----|-------|--------|------------|
| `name` | `"Toko Berkah Jaya"` | `env('APP_NAME')` | Nama aplikasi, tampil di UI |
| `env` | `"local"` | `env('APP_ENV')` | Environment mode |
| `debug` | `true` | `env('APP_DEBUG')` | Tampilkan stack trace saat error |
| `url` | `"http://localhost"` | `env('APP_URL')` | Base URL |
| `timezone` | `"Asia/Jakarta"` | Hardcoded | **Dihardcode di config, bukan di .env** |
| `locale` | `"id"` | `env('APP_LOCALE')` | Locale Bahasa Indonesia |
| `fallback_locale` | `"en"` | `env('APP_FALLBACK_LOCALE')` | Fallback English |
| `faker_locale` | `"en_US"` | `env('APP_FAKER_LOCALE')` | Locale untuk data dummy |
| `cipher` | `"AES-256-CBC"` | Hardcoded | Algoritma enkripsi |
| `key` | `"base64:irF0pc7..."` | `env('APP_KEY')` | Encryption key |
| `maintenance.driver` | `"file"` | `env('APP_MAINTENANCE_DRIVER')` | Maintenance mode storage |

### 3.2 Yang Perlu Diketahui

- **Timezone di-hardcode** ke `Asia/Jakarta` (UTC+7). Untuk mengubah, edit langsung di `config/app.php`, bukan `.env`.
- **APP_KEY** sudah di-generate. Jangan regenerate kecuali benar-benar perlu (akan meng-invalidate semua session & token).
- **APP_DEBUG = true** di production akan menampilkan detail error ke user → **MATIKAN di production**.

---

## 4. `config/database.php`

Lokasi: `config/database.php`

### 4.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `default` | `"mysql"` | Driver database aktif (dari `.env` `DB_CONNECTION=mysql`) |

### 4.2 Koneksi MySQL (Aktif)

```php
'connections' => [
    'mysql' => [
        'driver'   => 'mysql',
        'host'     => env('DB_HOST', '127.0.0.1'),     // 127.0.0.1
        'port'     => env('DB_PORT', '3306'),           // 8889
        'database' => env('DB_DATABASE', 'laravel'),    // inventory_web
        'username' => env('DB_USERNAME', 'root'),       // root
        'password' => env('DB_PASSWORD', ''),           // root
        'charset'  => env('DB_CHARSET', 'utf8mb4'),
        'collation'=> env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'strict'   => true,
    ],
]
```

### 4.3 Koneksi Lain yang Tersedia (Belum Aktif)

| Driver | Port Default | Catatan |
|--------|-------------|---------|
| `sqlite` | — | File-based, `database/database.sqlite` |
| `mysql` | 3306 | **Aktif** (dikonfigurasi di .env) |
| `mariadb` | 3306 | MySQL fork |
| `pgsql` | 5432 | PostgreSQL |
| `sqlsrv` | 1433 | Microsoft SQL Server |

### 4.4 Cara Beralih ke SQLite

1. Ubah `.env`:
   ```
   DB_CONNECTION=sqlite
   # DB_HOST=127.0.0.1
   # DB_PORT=8889
   # DB_DATABASE=inventory_web
   # DB_USERNAME=root
   # DB_PASSWORD=root
   ```
2. Buat file database:
   ```bash
   touch database/database.sqlite
   ```
3. Jalankan migration:
   ```bash
   php artisan migrate
   ```
4. Jalankan seeder:
   ```bash
   php artisan db:seed
   ```

### 4.5 Migration Repository

```php
'migrations' => [
    'table' => 'migrations',        // Tabel tracking migrasi
    'update_date_on_publish' => true,
],
```

### 4.6 Redis

```php
'redis' => [
    'client'  => env('REDIS_CLIENT', 'phpredis'),
    'default' => [
        'host'     => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
    'cache' => [
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
]
```

> Redis belum digunakan aktif saat ini. `CACHE_STORE` dan `QUEUE_CONNECTION` menggunakan `database`.

---

## 5. `config/auth.php`

Lokasi: `config/auth.php`

### 5.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `defaults.guard` | `"web"` | Guard utama |
| `defaults.passwords` | `"users"` | Password broker |
| `guards.web.driver` | `"session"` | Session-based auth |
| `guards.web.provider` | `"users"` | Provider: users |
| `providers.users.driver` | `"eloquent"` | Eloquent ORM |
| `providers.users.model` | `"App\Models\User"` | User model |
| `passwords.users.expire` | `60` | Token reset expired 60 menit |
| `passwords.users.throttle`` | `60` | Throttle 60 detik |
| `password_timeout` | `10800` | Password confirmation timeout (3 jam) |

### 5.2 Struktur Guard & Provider

```
Guard: web (session-based)
  └── Provider: users (Eloquent)
        └── Model: App\Models\User
              └── Trait: HasRoles (Spatie Permission)
```

### 5.3 Catatan

- Hanya ada **1 guard** (`web`). Tidak ada API token atau multi-auth.
- User model menggunakan **Spatie HasRoles** trait untuk role/permission.
- Password reset token expire: **60 menit**.
- Password confirmation timeout: **3 jam** (10800 detik).

---

## 6. `config/session.php`

Lokasi: `config/session.php`

### 6.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `driver` | `"database"` | Session disimpan di tabel `sessions` |
| `lifetime` | `120` | Session expired setelah 120 menit (2 jam) |
| `expire_on_close` | `false` | Session tidak expired saat browser ditutup |
| `encrypt` | `false` | Session tidak di-encrypt |
| `connection` | `null` | Default connection |
| `table` | `"sessions"` | Tabel penyimpanan session |
| `lottery` | `[2, 100]` | Kemungkinan sweep session: 2/100 |
| `cookie` | `"laravel-session"` | Nama cookie (berubah jika APP_NAME berubah) |
| `path` | `"/"` | Cookie path |
| `domain` | `null` | Cookie domain |
| `secure` | `null` | HTTPS only (null = auto) |
| `http_only` | `true` | Tidak bisa diakses via JavaScript |
| `same_site` | `"lax"` | SameSite cookie policy |
| `partitioned` | `false` | Partitioned cookies |

### 6.2 Tabel `sessions`

Dibuat oleh migration Laravel. Kolom:

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | varchar (PK) | Session ID |
| `user_id` | bigint (nullable) | ID user yang login |
| `ip_address` | varchar(45) | IP address |
| `user_agent` | text | Browser user agent |
| `payload` | longtext | Data session (serialized) |
| `last_activity` | int | Timestamp aktivitas terakhir |

### 6.3 Driver Lain yang Tersedia

| Driver | Keterangan |
|--------|------------|
| `file` | Disimpan di `storage/framework/sessions/` |
| `database` | **Aktif** — disimpan di tabel `sessions` |
| `redis` | Disimpan di Redis (perlu Redis server) |
| `memcached` | Disimpan di Memcached |
| `array` | In-memory (untuk testing) |

---

## 7. `config/cache.php`

Lokasi: `config/cache.php`

### 7.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `default` | `"database"` | Cache driver aktif |
| `prefix` | `"laravel-cache-"` | Prefix semua cache key |

### 7.2 Cache Store: Database

```php
'database' => [
    'driver'       => 'database',
    'connection'   => env('DB_CACHE_CONNECTION'),    // null (default)
    'table'        => env('DB_CACHE_TABLE', 'cache'), // tabel "cache"
    'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
    'lock_table'   => env('DB_CACHE_LOCK_TABLE'),
]
```

Tabel `cache` dibuat oleh migration Laravel.

### 7.3 Driver Lain yang Tersedia

| Driver | Catatan |
|--------|---------|
| `array` | In-memory, hilang saat request selesai |
| `database` | **Aktif** |
| `file` | Disimpan di `storage/framework/cache/data` |
| `redis` | Perlu Redis server |
| `memcached` | Perlu Memcached server |
| `dynamodb` | Amazon DynamoDB |

---

## 8. `config/queue.php`

Lokasi: `config/queue.php`

### 8.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `default` | `"database"` | Queue driver aktif |
| `database.table` | `"jobs"` | Tabel penyimpanan job |
| `database.queue` | `"default"` | Nama queue |
| `database.retry_after` | `90` | Retry job setelah 90 detik |
| `failed.driver` | `"database-uuids"` | Failed jobs disimpan di database |
| `failed.table` | `"failed_jobs"` | Tabel failed jobs |

### 8.2 Cara Menjalankan Queue Worker

```bash
php artisan queue:listen          # Single worker
php artisan queue:listen --queue=default  # Queue tertentu
php artisan queue:work            # Worker terus-menerus
```

> Dalam mode development, script `composer dev` sudah menjalankan `queue:listen` secara otomatis.

### 8.3 Driver Lain

| Driver | Catatan |
|--------|---------|
| `sync` | Eksekusi sinkron (tidak ada queue) |
| `database` | **Aktif** — menggunakan tabel `jobs` |
| `redis` | Perlu Redis server |
| `beanstalkd` | Perlu Beanstalkd server |
| `sqs` | Amazon SQS |

---

## 9. `config/mail.php`

Lokasi: `config/mail.php`

### 9.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `default` | `"log"` | Mailer aktif: log (tidak kirim email asli) |
| `from.address` | `"hello@example.com"` | Alamat email pengirim |
| `from.name` | `"Toko Berkah Jaya"` | Nama email pengirim |

### 9.2 Mailer: Log (Aktif)

```php
'log' => [
    'transport' => 'log',
    'channel'   => env('MAIL_LOG_CHANNEL'),  // null
]
```

Email tidak dikirim — hanya ditulis ke log file di `storage/logs/laravel.log`.

### 9.3 Mailer Lain yang Tersedia

| Mailer | Keterangan |
|--------|------------|
| `log` | **Aktif** — email ditulis ke log |
| `smtp` | SMTP server (perlu konfigurasi host/port/username/password) |
| `ses` | Amazon SES |
| `postmark` | Postmark |
| `resend` | Resend |
| `sendmail` | Sendmail binary |
| `array` | In-memory (testing) |
| `failover` | Fallback: smtp → log |
| `roundrobin` | Load balance: ses → postmark |

### 9.4 Contoh Konfigurasi SMTP (untuk production)

Ubah `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@tokoberkahjaya.com
MAIL_FROM_NAME="Toko Berkah Jaya"
MAIL_SCHEME=tls
```

---

## 10. `config/logging.php`

Lokasi: `config/logging.php`

### 10.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `default` | `"stack"` | Log channel default |
| `deprecations.channel` | `"null"` | Deprecation log ke null |
| `channels.stack.channels` | `["single"]` | Stack berisi channel "single" |
| `channels.single.path` | `storage/logs/laravel.log` | Path log file |
| `channels.single.level` | `"debug"` | Level log |

### 10.2 Log Channels yang Tersedia

| Channel | Keterangan |
|---------|------------|
| `stack` | **Aktif** — menggabungkan beberapa channel |
| `single` | Satu file: `storage/logs/laravel.log` |
| `daily` | File harian, rotate setiap N hari |
| `slack` | Kirim log ke Slack |
| `papertrail` | Papertrail logging service |
| `stderr` | Output ke stderr (CLI) |
| `syslog` | Syslog |
| `errorlog` | PHP error_log |
| `null` | Buang semua log |
| `emergency` | Fallback: `storage/logs/laravel.log` |

### 10.3 Log Levels

```
debug → info → notice → warning → error → critical → alert → emergency
```

| Level | Kapan Digunakan |
|-------|-----------------|
| `debug` | Informasi detail untuk debugging |
| `info` | Informasi umum (transaksi berhasil, dll) |
| `warning` | Peringatan (deprecated function, dll) |
| `error` | Error yang perlu diperhatikan |
| `critical` | Error serius (database down, dll) |

---

## 11. `config/filesystems.php`

Lokasi: `config/filesystems.php`

### 11.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `default` | `"local"` | Disk default |

### 11.2 Disk: Local (Aktif)

```php
'local' => [
    'driver'  => 'local',
    'root'    => storage_path('app/private'),
    'serve'   => true,
    'throw'   => false,
    'report'  => false,
]
```

File disimpan di `storage/app/private/`.

### 11.3 Disk Lain yang Tersedia

| Disk | Driver | Root | Keterangan |
|------|--------|------|------------|
| `local` | local | `storage/app/private` | **Aktif** — private storage |
| `public` | local | `storage/app/public` | Public, bisa diakses via URL |
| `s3` | s3 | Amazon S3 | Cloud storage |

### 11.4 Symbolic Links

```php
'links' => [
    public_path('storage') => storage_path('app/public'),
]
```

Untuk membuat symlink:
```bash
php artisan storage:link
```

---

## 12. `config/services.php`

Lokasi: `config/services.php`

Konfigurasi API key untuk layanan third-party.

| Service | Keys | Status |
|---------|------|--------|
| `postmark` | `key` | Belum dikonfigurasi |
| `resend` | `key` | Belum dikonfigurasi |
| `ses` | `key`, `secret`, `region` | Belum dikonfigurasi |
| `slack` | `bot_user_oauth_token`, `channel` | Belum dikonfigurasi |

> Semua service third-party belum dikonfigurasi saat ini. Tambahkan API key ke `.env` sesuai kebutuhan.

---

## 13. `config/permission.php`

Lokasi: `config/permission.php`

Konfigurasi untuk **Spatie Laravel Permission** v7.0.

### 13.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `models.permission` | `Spatie\Permission\Models\Permission` | Model Permission |
| `models.role` | `Spatie\Permission\Models\Role` | Model Role |
| `models.team` | `null` | Tidak pakai team |
| `table_names.roles` | `"roles"` | Tabel roles |
| `table_names.permissions` | `"permissions"` | Tabel permissions |
| `table_names.model_has_permissions` | `"model_has_permissions"` | Pivot: model ↔ permissions |
| `table_names.model_has_roles` | `"model_has_roles"` | Pivot: model ↔ roles |
| `table_names.role_has_permissions` | `"role_has_permissions"` | Pivot: roles ↔ permissions |
| `teams` | `false` | **Fitur teams dinonaktifkan** |
| `events_enabled` | `false` | Event role/permission dinonaktifkan |
| `enable_wildcard_permission` | `false` | Wildcard permission dinonaktifkan |
| `cache.expiration_time` | `24 hours` | Permission cache expired 24 jam |
| `display_permission_in_exception` | `false` | Jangan tampilkan permission di error |
| `display_role_in_exception` | `false` | Jangan tampilkan role di error |

### 13.2 Database Tables (5 tabel)

```
permissions          ← daftar semua permission
roles                ← daftar semua role
model_has_permissions ← pivot: model ↔ permission (polymorphic)
model_has_roles      ← pivot: model ↔ role (polymorphic)
role_has_permissions ← pivot: role ↔ permission
```

### 13.3 Catatan Penting

- **Teams fitur dinonaktifkan** (`teams = false`). Jika ingin mengaktifkan, harus migrasi ulang.
- **Cache permission 24 jam**. Setelah update role/permission, cache otomatis di-flush.
- **Events dinonaktifkan**. Untuk listening event role/permission, set `events_enabled = true`.

---

## 14. `config/filament-shield.php`

Lokasi: `config/filament-shield.php`

Konfigurasi untuk **BezhanSalleh Filament Shield** v4.2 — UI manajemen role/permission di panel admin.

### 14.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `shield_resource.slug` | `"shield/roles"` | URL halaman manajemen role |
| `shield_resource.show_model_path` | `true` | Tampilkan model path di UI |
| `shield_resource.tabs.pages` | `true` | Tab pages di permission |
| `shield_resource.tabs.widgets` | `true` | Tab widgets di permission |
| `shield_resource.tabs.resources` | `true` | Tab resources di permission |
| `shield_resource.tabs.custom_permissions` | `false` | Tab custom permissions |
| `tenant_model` | `null` | Tidak pakai multi-tenancy |
| `auth_provider_model` | `"App\Models\User"` | User model |
| `super_admin.enabled` | `true` | Super admin role aktif |
| `super_admin.name` | `"super_admin"` | Nama role super admin |
| `super_admin.define_via_gate` | `false` | Super admin via role, bukan gate |
| `panel_user.enabled` | `true` | Panel user role aktif |
| `panel_user.name` | `"panel_user"` | Nama role panel user |
| `permissions.separator` | `":"` | Separator permission: `viewAny:Product` |
| `permissions.case` | `"pascal"` | Case permission: `viewAny` (bukan `view_any`) |
| `permissions.generate` | `true` | Auto-generate permission |
| `policies.generate` | `true` | Auto-generate policy |
| `policies.merge` | `true` | Merge dengan policy yang sudah ada |
| `policies.methods` | `[viewAny, view, create, ...]` | 12 method per model |
| `pages.prefix` | `"view"` | Permission prefix untuk pages |
| `pages.exclude` | `[Dashboard]` | Dashboard tidak perlu permission |
| `widgets.prefix` | `"view"` | Permission prefix untuk widgets |
| `widgets.exclude` | `[AccountWidget, FilamentInfoWidget]` | Widget default di-exclude |
| `register_role_policy` | `true` | Register policy untuk role management |

### 14.2 Permission Naming Pattern

```
{method}:{Model}
```

Contoh:
- `viewAny:Product` — melihat daftar produk
- `create:StockIn` — membuat barang masuk
- `delete:Category` — menghapus kategori

### 14.3 Auto-Generated Policies

Untuk setiap model, Shield auto-generate 12 method:

| Method | Keterangan |
|--------|------------|
| `viewAny` | Melihat daftar |
| `view` | Melihat detail |
| `create` | Membuat baru |
| `update` | Mengubah |
| `delete` | Menghapus |
| `deleteAny` | Menghapus beberapa |
| `restore` | Mengembalikan (soft delete) |
| `forceDelete` | Hapus permanen |
| `forceDeleteAny` | Hapus permanen beberapa |
| `restoreAny` | Mengembalikan beberapa |
| `replicate` | Meng duplikat |
| `reorder` | Mengubah urutan |

---

## 15. `AdminPanelProvider.php`

Lokasi: `app/Providers/Filament/AdminPanelProvider.php`

Konfigurasi panel admin Filament.

### 15.1 Setting Aktif

| Key | Nilai | Keterangan |
|-----|-------|------------|
| `->default()` | — | Panel default |
| `->brandLogo(asset('logo.png'))` | Logo dari `/public/logo.png` | Logo sidebar |
| `->brandName(config('app.name'))` | `"Toko Berkah Jaya"` | Nama di sidebar |
| `->favicon(asset('logo.png'))` | Logo dari `/public/logo.png` | Icon tab browser |
| `->brandLogoHeight('4rem')` | 4rem | Tinggi logo |
| `->id('admin')` | `"admin"` | Panel ID |
| `->path('admin')` | `"admin"` | URL path: `/admin` |
| `->login()` | — | Halaman login aktif |
| `->spa()` | — | SPA mode aktif |
| `->colors(['primary' => Color::Amber])` | Amber | Warna tema: kuning/amber |

### 15.2 Resource & Page Discovery

```php
->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
```

Semua resource, page, dan widget di `app/Filament/` **auto-discovered**.

### 15.3 Plugins

```php
->plugins([
    FilamentShieldPlugin::make()
        ->navigationSort(20),   // Shield di urutan ke-20 di sidebar
])
```

Hanya 1 plugin: **FilamentShield** untuk manajemen role/permission.

### 15.4 Middleware

```php
->middleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    AuthenticateSession::class,
    ShareErrorsFromSession::class,
    VerifyCsrfToken::class,
    SubstituteBindings::class,
    DisableBladeIconComponents::class,
    DispatchServingFilamentEvent::class,
])
->authMiddleware([
    Authenticate::class,
])
```

### 15.5 User Menu

```php
->userMenuItems([
    Action::make('settings')
        ->url(fn (): string => Settings::getUrl())
        ->icon('heroicon-o-cog-6-tooth')
])
```

Menu "Pengaturan" di dropdown profil user.

### 15.6 SPA Mode

SPA mode diaktifkan (`->spa()`). Ini berarti:
- Navigasi antar halaman menggunakan AJAX (tidak full page reload)
- Lebih cepat untuk user experience
- Beberapa halaman menggunakan `?page=` query parameter

---

## 16. Panduan Deploy

### 16.1 Checklist Sebelum Deploy

- [ ] **APP_ENV** = `production`
- [ ] **APP_DEBUG** = `false`
- [ ] **APP_URL** = URL production (https://...)
- [ ] **APP_KEY** sudah ada (jangan commit ke repo)
- [ ] **Database** sudah dibuat dan migration dijalankan
- [ ] **DB_HOST**, **DB_PORT**, **DB_DATABASE**, **DB_USERNAME**, **DB_PASSWORD** sudah benar
- [ ] **MAIL_MAILER** dikonfigurasi (bukan `log`)
- [ ] **MAIL_FROM_ADDRESS** diubah ke email production
- [ ] **SESSION_DRIVER** = `database` atau `redis`
- [ ] **QUEUE_CONNECTION** = `database` atau `redis`
- [ ] **CACHE_STORE** = `database` atau `redis`
- [ ] File `.env` tidak di-commit ke repository
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `npm install && npm run build`
- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed` (jika perlu)
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan storage:link`
- [ ] Queue worker dijalankan: `php artisan queue:work --daemon`
- [ ] Cron job untuk queue scheduler sudah diatur

### 16.2 Perintah Deploy (Lengkap)

```bash
# 1. Clone repository
git clone <repo-url>
cd Inventory-web

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate
# Edit .env sesuai kebutuhan production

# 4. Database
php artisan migrate --force
php artisan db:seed --force  # optional

# 5. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Storage link
php artisan storage:link

# 7. Permissions (Linux)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 8. Queue worker (dalam background)
php artisan queue:work --daemon --sleep=3 --tries=3 &

# 9. Scheduler (cron)
# Tambahkan di crontab:
# * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 16.3 Server Requirements

| Komponen | Versi Minimum |
|----------|--------------|
| PHP | 8.2 |
| Extensions | OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath |
| MySQL | 5.7+ / MariaDB 10.3+ |
| Node.js | 18+ (untuk build assets) |
| Composer | 2.x |

### 16.4 Nginx Config (Contoh)

```nginx
server {
    listen 80;
    server_name inventory.tokoberkahjaya.com;
    root /var/www/Inventory-web/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 17. Troubleshooting

### 17.1 Error: "No application encryption key has been specified"

**Penyebab:** `APP_KEY` kosong atau `.env` tidak terbaca.

**Solusi:**
```bash
php artisan key:generate
# atau pastikan APP_KEY ada di .env
```

### 17.2 Error: "SQLSTATE[HY000] Connection refused"

**Penyebab:** Database server tidak berjalan atau kredensial salah.

**Solusi:**
1. Pastikan MySQL/MariaDB berjalan
2. Cek `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` di `.env`
3. Pastikan database `inventory_web` sudah dibuat

### 17.3 Error: "View [filament-panels::components.logo] not found"

**Penyebab:** Filament belum di-upgrade atau cache view corrupt.

**Solusi:**
```bash
composer update filament/filament
php artisan view:clear
php artisan view:cache
```

### 17.4 Error: "Permission defined but role does not exist"

**Penyebab:** RoleSeeder belum dijalankan.

**Solusi:**
```bash
php artisan shield:generate --all
# atau
php artisan db:seed --class=RoleSeeder
```

### 17.5 Error: Session expired tiba-tiba

**Penyebab:** Session lifetime terlalu pendek atau driver session bermasalah.

**Solusi:**
1. Cek `SESSION_LIFETIME` di `.env` (default 120 menit)
2. Cek `SESSION_DRIVER` (harus `database` atau `redis`)
3. Pastikan tabel `sessions` ada dan bisa diakses

### 17.6 Error: "The CSRF token is missing"

**Penyebab:** Form POST tanpa CSRF token atau session bermasalah.

**Solusi:**
1. Pastikan `SESSION_DRIVER` berfungsi
2. Clear cache: `php artisan cache:clear`
3. Restart browser

### 17.7 Queue Job Tidak Diproses

**Penyebab:** Queue worker tidak berjalan.

**Solusi:**
```bash
# Cek queue worker
ps aux | grep queue:work

# Jalankan queue worker
php artisan queue:work --daemon --sleep=3 --tries=3

# Cek failed jobs
php artisan queue:failed
php artisan queue:retry all
```

### 17.8 Config Cache Tidak Update

**Penyebab:** Config sudah di-cache, perubahan di `.env` tidak terbaca.

**Solusi:**
```bash
php artisan config:clear
php artisan config:cache
```

---

## Appendix: Mapping .env → config/*.php

| .env Variable | config File | Key |
|--------------|-------------|-----|
| `APP_NAME` | `config/app.php` | `name` |
| `APP_ENV` | `config/app.php` | `env` |
| `APP_DEBUG` | `config/app.php` | `debug` |
| `APP_URL` | `config/app.php` | `url` |
| `APP_KEY` | `config/app.php` | `key` |
| `APP_LOCALE` | `config/app.php` | `locale` |
| `DB_CONNECTION` | `config/database.php` | `default` |
| `DB_HOST` | `config/database.php` | `connections.mysql.host` |
| `DB_PORT` | `config/database.php` | `connections.mysql.port` |
| `DB_DATABASE` | `config/database.php` | `connections.mysql.database` |
| `DB_USERNAME` | `config/database.php` | `connections.mysql.username` |
| `DB_PASSWORD` | `config/database.php` | `connections.mysql.password` |
| `SESSION_DRIVER` | `config/session.php` | `driver` |
| `SESSION_LIFETIME` | `config/session.php` | `lifetime` |
| `SESSION_ENCRYPT` | `config/session.php` | `encrypt` |
| `CACHE_STORE` | `config/cache.php` | `default` |
| `QUEUE_CONNECTION` | `config/queue.php` | `default` |
| `MAIL_MAILER` | `config/mail.php` | `default` |
| `MAIL_HOST` | `config/mail.php` | `mailers.smtp.host` |
| `MAIL_PORT` | `config/mail.php` | `mailers.smtp.port` |
| `MAIL_USERNAME` | `config/mail.php` | `mailers.smtp.username` |
| `MAIL_PASSWORD` | `config/mail.php` | `mailers.smtp.password` |
| `LOG_CHANNEL` | `config/logging.php` | `default` |
| `LOG_LEVEL` | `config/logging.php` | `channels.single.level` |
| `FILESYSTEM_DISK` | `config/filesystems.php` | `default` |

---

*Dokumen ini dibuat untuk kebutuhan internal tim teknis. Untuk pertanyaan, hubungi lead developer.*
