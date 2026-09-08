# Sistem Rekrutmen Ormawa

Aplikasi web pengelolaan rekrutmen organisasi mahasiswa Polstat STIS. Login memakai Google OAuth untuk akun kampus, dengan ruang kerja terpisah untuk organisasi, panitia, dan mahasiswa.

## Kebutuhan server

- PHP 8.3+ dengan `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `fileinfo`, `xml`, `zip`, dan `ctype`.
- Composer 2, Node.js 20+, npm, serta MySQL/MariaDB.
- Nginx atau Apache, HTTPS, dan kredensial Google OAuth.

> Document root web server **harus** diarahkan ke folder `public`, bukan root repository.

## Deploy dari GitHub

Ganti URL contoh dengan URL repository GitHub aplikasi.

```bash
git clone https://github.com/USERNAME/NAMA-REPOSITORY.git rekrutmen-ormawa
cd rekrutmen-ormawa
git checkout main

composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
```

Salin template environment lalu buat application key.

```bash
cp .env.example .env
php artisan key:generate
```

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

## Konfigurasi production

Edit `.env` di server. Jangan pernah mengunggah file ini ke GitHub.

```dotenv
APP_NAME="Sistem Rekrutmen Ormawa"
APP_ENV=production
APP_KEY=             # diisi oleh php artisan key:generate
APP_DEBUG=false
APP_URL=https://rekrutmen.contoh.ac.id
APP_TIMEZONE=Asia/Jakarta

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rekrutmen_ormawa
DB_USERNAME=database_user
DB_PASSWORD=database_password_yang_kuat

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

GOOGLE_CLIENT_ID=xxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=isi_client_secret_google
GOOGLE_CLIENT_REDIRECT=https://rekrutmen.contoh.ac.id/auth/google/callback
```

Buat database kosong sesuai `DB_DATABASE`, lalu jalankan:

```bash
php artisan migrate --force
php artisan storage:link
```

Jika database benar-benar baru dan organisasi awal belum tersedia, jalankan seeder sekali saja:

```bash
php artisan db:seed --class=OrganisasiSeeder --force
```

Seeder menambahkan organisasi DPM dan BEM. Jangan menjalankannya kembali pada database yang sudah memiliki data organisasi.

Untuk memberi akses role Dosen, tambahkan nama dan email kampusnya ke tabel `dosen` (misalnya melalui phpMyAdmin atau tinker). Email harus sama dengan akun Google yang digunakan untuk login:

```bash
php artisan tinker --execute="App\\Models\\Dosen::firstOrCreate(['email' => 'nama.dosen@stis.ac.id'], ['nama' => 'Nama Dosen'])"
```

## Konfigurasi Google OAuth

Di Google Cloud Console, buat OAuth Client ID jenis **Web application**, lalu tambahkan:

- Authorized JavaScript origin: `https://rekrutmen.contoh.ac.id`
- Authorized redirect URI: `https://rekrutmen.contoh.ac.id/auth/google/callback`

Salin Client ID, Client Secret, dan redirect URI ke variabel `GOOGLE_*` pada `.env`. Mahasiswa hanya dapat masuk dengan format email `NIM@stis.ac.id`; akun organisasi harus tersedia pada kolom `organisasi.email_kampus`.

## Permission Linux

Jalankan dari root repository dan sesuaikan pengguna web server bila bukan `www-data`.

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

## Konfigurasi Nginx

```nginx
server {
    listen 80;
    server_name rekrutmen.contoh.ac.id;
    root /var/www/rekrutmen-ormawa/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\. {
        deny all;
    }
}
```

Gunakan HTTPS, misalnya dengan Certbot, sebelum mengaktifkan Google OAuth di domain produksi.

## Optimasi produksi

Jalankan setelah `.env` final.

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache
```

## Memperbarui aplikasi dari GitHub

Cadangkan database, kemudian jalankan:

```bash
php artisan down
git pull origin main
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache
php artisan up
```

Apabila update gagal, jangan menjalankan `php artisan up` sebelum error ditangani. Periksa `storage/logs/laravel.log` dan pulihkan database bila diperlukan.

## Pemeriksaan sebelum go-live

```bash
php artisan test
npm run build
```

Pastikan `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` dan `GOOGLE_CLIENT_REDIRECT` memakai domain HTTPS yang sama, serta folder `storage` dan `bootstrap/cache` dapat ditulis web server.
