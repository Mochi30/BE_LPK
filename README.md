# ENTREPREXA Company Profile

Project ini dipisahkan menjadi dua folder:
- `frontend` untuk React/Vite
- `backend` untuk Laravel API

## Prasyarat
- PHP 8.2+
- Composer 2+
- Node.js 18+

## Menjalankan Frontend
1. Buka terminal di `e:\Azam\LPK Kewirausahaan\frontend`.
2. Jalankan `npm install` jika belum.
3. Jalankan `npm run dev`.
4. Buka URL yang muncul di terminal (biasanya `http://localhost:5175/`).

## Menjalankan Backend (Laravel API)
1. Buka terminal di `e:\Azam\LPK Kewirausahaan\backend`.
2. Jalankan `composer install` jika belum.
3. Pastikan file `database/database.sqlite` ada.
4. Jalankan `php artisan key:generate` jika `APP_KEY` kosong.
5. Jalankan `php artisan migrate`.
6. Jalankan `php artisan serve`.

Catatan: jika migrasi gagal dengan error `could not find driver`, aktifkan ekstensi `pdo_sqlite` di `php.ini` atau ganti ke MySQL di `.env`.

## Endpoint API (prefix `/api`)
- `GET /home`
- `CRUD /programs`
- `CRUD /instructors`
- `CRUD /testimonials`
- `CRUD /articles`
- `CRUD /gallery`
- `CRUD /faqs`
- `CRUD /impact-stats`
- `POST /contact-messages`
- `POST /leads`
- `POST /newsletter`

## Struktur Folder
- `frontend/` React + Vite
- `backend/` Laravel API
