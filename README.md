# OurMemora

Set Up And Instalation

## Prasyarat

Pastikan lingkungan pengembangan sudah terpasang:

- PHP 8.1 atau lebih tinggi
- Composer
- Node.js dan npm/yarn
- Database MySQL atau MariaDB
- Server lokal seperti Laragon / XAMPP / Valet

## Clone Repository

1. Clone repo ke folder lokal:

```bash
git clone <URL_REPO> MyGallery
cd MyGallery
```

2. Copy file konfigurasi environment:

```bash
copy .env.example .env
```

## Install Dependensi

1. Install dependensi PHP:

```bash
composer install
```

2. Install dependensi JavaScript:

```bash
npm install
```

atau jika pakai yarn:

```bash
yarn install
```

> Jika repo ini belum memiliki Socialite, jalankan:
>
> ```bash
> composer require laravel/socialite
> ```

## Konfigurasi Environment

Edit file `.env` dan sesuaikan nilai berikut:

```dotenv
APP_NAME=MyGallery
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public

MAIL_MAILER=log
```

Jika menggunakan Laragon dan custom domain, ubah `APP_URL` sesuai konfigurasi domain lokal.

### Google Socialite

Proyek ini sudah terkonfigurasi untuk login Google. Tambahkan variabel berikut di `.env`:

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Pastikan URL callback ini sama dengan yang diatur di Google Cloud Console.

## Generate Application Key

```bash
php artisan key:generate
```

## Migrasi dan Seed Database

Buat database kosong di MySQL/MariaDB, lalu jalankan:

```bash
php artisan migrate
```

Jika ingin mengisi data awal:

```bash
php artisan db:seed
```

Untuk memulai ulang database dan seed sekaligus:

```bash
php artisan migrate:fresh --seed
```

## Storage Link

Agar foto bisa diakses lewat browser, jalankan:

```bash
php artisan storage:link
```

## Build Aset Frontend

Untuk build produksi:

```bash
npm run build
```

Untuk mode development dengan hot reload:

```bash
npm run dev
```

## Jalankan Server Lokal

```bash
php artisan serve
```

Buka browser di:

```text
http://127.0.0.1:8000
```

## Menampilkan Foto di Website

- Landing page utama menampilkan foto publik sebagai grid thumbnail.
- Klik foto untuk melihat halaman detail foto.
- Setelah login, buka dashboard untuk upload foto, melihat foto publik, memberi like, save, dan download.
- Foto disimpan di `storage/app/public` dan diakses lewat `public/storage` setelah menjalankan `php artisan storage:link`.

## Login Google

Route Google login yang tersedia:

- `/auth/google`
- `/auth/google/callback`

Tombol "Lanjutkan dengan Google" pada login modal akan menggunakan route di atas.

## Troubleshooting

- Pastikan file `.env` sudah terisi dengan benar.
- Pastikan database sudah dibuat dan terhubung.
- Pastikan `php artisan storage:link` sudah dijalankan.
- Pastikan folder `storage` dan `bootstrap/cache` dapat ditulisi.
- Jika asset tidak muncul, jalankan ulang `npm run dev` atau `npm run build`.
- Jika login Google gagal, cek kembali setelan `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, dan `GOOGLE_REDIRECT_URI`.

---
