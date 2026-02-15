# Cara Upload Laravel ke InfinityFree

## Struktur Folder di Local:

```
Laravel Project/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← INI yang naik ke htdocs (isi nya saja)
│   ├── index.php    ← Sudah diedit
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── ...
├── resources/
├── routes/
├── storage/         ← WAJIB naikkan semua
├── vendor/
├── .env
├── artisan
└── composer.json
```

## Langkah Upload:

### 1. Edit file `public/index.php` (SUDAH DIBUAT)

### 2. Yang harus di-UPLOAD ke htdocs:

```
htdocs/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← HAPUS folder ini! Isi nya langsung di htdocs
│   └── (pindahkan semua ke htdocs)
├── resources/
├── routes/
├── storage/         ← WAJIB!
├── vendor/         ← WAJIB!
├── .env            ← Edit dulu sesuai database InfinityFree
├── .htaccess
├── artisan
├── composer.json
├── composer.lock
└── phpunit.xml
```

### 3. Yang TIDAK perlu di-UPLOAD:
- `node_modules/`
- `.git/`
- `tests/`
- `vendor/` (sudah dalam bentuk zip/arsip)
- File .env local (bukan .env production)

### 4. Setup Database InfinityFree:

Edit `.env` dengan data dari InfinityFree:
```env
APP_NAME=SocialChat
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:IMH8lqsxdJa+UqFW+USjwwFpah2Ni2Hezn8OBod5ocQ=

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_infinityfree_db_name
DB_USERNAME=your_infinityfree_username
DB_PASSWORD=your_infinityfree_password

LOG_CHANNEL=errorlog
```

### 5. Setup Storage Link:

Di File Manager InfinityFree, buat symbolic link atau手动 copy isi `storage/app/public/` ke folder yang bisa diakses.

### 6. PENTING - Set Permissions:

- `storage/` - 755 or 775
- `bootstrap/cache/` - 755 or 775

### 7. Running Migrations:

Buat file `migrate.php` di htdocs:
```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
Illuminate\Support\Facades\Artisan::call('migrate --force');
echo 'Migration complete';
```

Buka `https://domainkamu.com/migrate.php` di browser.

---

## Jika Still Error:

Cek error log di InfinityFree → Files → Logs

atau buat file `debug.php`:
```php
<?php
phpinfo();
```
