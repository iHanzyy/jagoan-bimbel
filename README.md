# Jagoan Bimbel - fitur manajemen soal

Aplikasi ini dibuat untuk technical test "Fitur Manajemen Soal". Studi kasusnya adalah platform bimbingan belajar dengan dua role: Admin dan Siswa.

Admin mengelola soal dalam format PDF, gambar, dan link YouTube. Siswa melihat daftar soal dan membuka detail materi langsung dari browser.

## Ringkasan

Yang sudah dikerjakan:

- Login berbasis Laravel Sanctum.
- Role `admin` dan `siswa`.
- Admin bisa upload, melihat daftar, edit, delete, download file PDF/gambar, dan copy link YouTube.
- Siswa bisa melihat daftar soal dan membuka detail soal.
- Preview detail siswa:
  - PDF lewat iframe.
  - Image lewat tag `img`.
  - YouTube lewat embedded iframe.
- Validasi upload berbeda untuk PDF, image, dan YouTube.
- File PDF/image disimpan di storage public.
- List materi memakai cache dengan TTL 300 detik.
- Cache list dihapus saat materi dibuat, diubah, atau dihapus.
- UI dibuat responsive untuk desktop, tablet, dan mobile.

## Kesesuaian dengan technical test

| Requirement | Status | Implementasi |
| --- | --- | --- |
| Admin upload soal | Selesai | `POST /api/upload-materi`, halaman `/admin/materi/create` |
| Tipe PDF/Image/YouTube | Selesai | Enum `MateriType`, validasi form request |
| Admin melihat daftar soal | Selesai | `/admin/materi`, `GET /api/list-materi` |
| Admin edit dan delete | Selesai | `/admin/materi/{id}/edit`, `PUT /api/update-materi/{id}`, `DELETE /api/delete-materi/{id}` |
| Admin download PDF/Image | Selesai | `GET /api/materi-download/{id}` |
| Admin salin link YouTube | Selesai | Tombol copy link di daftar admin |
| Siswa melihat daftar soal | Selesai | `/siswa/materi` |
| Siswa melihat detail soal | Selesai | `/siswa/materi/{id}`, `GET /api/detail-materi/{id}` |
| Preview PDF/Image/YouTube | Selesai | Blade + Alpine di halaman detail siswa |
| Migration database | Selesai | `users.role`, `file_materis` |
| UI responsive | Selesai | Blade, Tailwind CSS, reusable dashboard components |
| Caching | Selesai | Cache list materi per role dan page |

## Tech stack

- PHP `^8.3`
- Laravel `13.x`
- MySQL
- Laravel Sanctum
- Blade
- Alpine.js
- Tailwind CSS `4.x`
- SweetAlert2
- Vite

## Cara menjalankan

### 1. Clone repository

```bash
git clone <repository-url>
cd jagoan-bimbel
```

### 2. Install dependency backend

```bash
composer install
```

### 3. Install dependency frontend

```bash
npm install
```

### 4. Siapkan file environment

```bash
cp .env.example .env
```

Untuk Windows PowerShell:

```powershell
copy .env.example .env
```

### 5. Generate app key

```bash
php artisan key:generate
```

### 6. Buat database

Contoh untuk MySQL:

```sql
CREATE DATABASE jagoan_bimbel;
```

Lalu sesuaikan `.env`:

```env
APP_NAME="Jagoan Bimbel"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jagoan_bimbel
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### 7. Jalankan migration dan seeder

```bash
php artisan migrate:fresh --seed
```

Seeder membuat satu akun admin dan satu akun siswa.

### 8. Buat storage link

```bash
php artisan storage:link
```

Ini wajib untuk preview PDF dan image, karena file disimpan di `storage/app/public` dan dibaca lewat URL `/storage/...`.

### 9. Jalankan Laravel

```bash
php artisan serve
```

### 10. Jalankan Vite

```bash
npm run dev
```

Buka aplikasi:

```text
http://127.0.0.1:8000
```

## Akun demo

Admin:

```text
Email: admin@example.com
Password: password
```

Siswa:

```text
Email: siswa@example.com
Password: password
```

## Halaman web

| Role | URL | Keterangan |
| --- | --- | --- |
| Public | `/login` | Login admin dan siswa |
| Admin | `/admin/materi` | Daftar soal admin |
| Admin | `/admin/materi/create` | Upload soal |
| Admin | `/admin/materi/{id}/edit` | Edit soal |
| Siswa | `/siswa/materi` | Daftar soal siswa |
| Siswa | `/siswa/materi/{id}` | Detail dan preview soal |

## Alur penggunaan

### Admin

1. Login memakai akun admin.
2. Buka `/admin/materi`.
3. Upload soal lewat tombol "Upload Soal".
4. Pilih tipe:
   - `pdf`: upload file PDF.
   - `image`: upload JPG, JPEG, atau PNG.
   - `youtube`: isi URL YouTube.
5. Setelah tersimpan, materi muncul di daftar.
6. Admin bisa edit, delete, download PDF/image, atau copy link YouTube.

### Siswa

1. Login memakai akun siswa.
2. Buka `/siswa/materi`.
3. Pilih materi.
4. Detail materi akan menampilkan preview sesuai tipe:
   - PDF tampil di iframe, dengan tombol buka tab baru dan download.
   - Image tampil langsung, dengan tombol buka gambar dan download.
   - YouTube tampil sebagai video embed.

## API authentication

API memakai token Laravel Sanctum.

Login:

```http
POST /api/login
```

Body:

```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

Response login mengembalikan `access_token` dan `token_type`. Kirim token itu pada request berikutnya:

```http
Authorization: Bearer <access_token>
Accept: application/json
```

Logout:

```http
POST /api/logout
```

## API admin

### List materi

```http
GET /api/list-materi
```

Role: `admin` atau `siswa`

Query:

```text
page=1
```

### Detail materi untuk admin

```http
GET /api/show-materi/{id}
```

Role: `admin`

### Upload materi

```http
POST /api/upload-materi
```

Role: `admin`

Untuk PDF:

```text
title: Soal Matematika
description: Soal Matematika Aljabar
type: pdf
file: matematika.pdf
```

Untuk image:

```text
title: Soal Bahasa Inggris
description: Latihan Bahasa Inggris
type: image
file: soal.png
```

Untuk YouTube:

```text
title: Video Informatika
description: Materi Informatika
type: youtube
youtube_url: https://youtu.be/<video-id>
```

### Update materi

```http
PUT /api/update-materi/{id}
```

Role: `admin`

Catatan:

- Jika tipe tetap PDF/image dan file tidak diganti, file lama tetap dipakai.
- Jika tipe berubah dari YouTube ke PDF/image, file baru wajib dikirim.
- Jika tipe berubah ke YouTube, `youtube_url` wajib dikirim dan `file_path` akan dikosongkan.

### Delete materi

```http
DELETE /api/delete-materi/{id}
```

Role: `admin`

Jika materi punya file PDF/image, file di storage ikut dihapus.

### Download materi

```http
GET /api/materi-download/{id}
```

Role: `admin`

Endpoint ini hanya untuk materi tipe `pdf` dan `image`.

## API siswa

### List materi

```http
GET /api/list-materi
```

Role: `siswa`

### Detail materi

```http
GET /api/detail-materi/{id}
```

Role: `siswa`

Response detail berisi data materi, termasuk:

```json
{
  "id": 1,
  "title": "Soal Matematika",
  "description": "Soal Matematika Aljabar",
  "type": "pdf",
  "file_path": "materis/pdf/example.pdf",
  "file_url": "/storage/materis/pdf/example.pdf",
  "youtube_url": null,
  "youtube_embed_url": null,
  "created_by": 1,
  "created_at": "2026-06-06T09:00:00.000000Z",
  "updated_at": "2026-06-06T09:00:00.000000Z"
}
```

`file_url` dibuat relative ke host aplikasi (`/storage/...`) supaya tetap aman saat app dijalankan dari `127.0.0.1`, Laragon vhost, atau host lokal lain.

## Aturan validasi materi

PDF:

- `title` wajib.
- `description` wajib.
- `type` harus `pdf`.
- `file` wajib.
- MIME harus `application/pdf`.
- `youtube_url` tidak boleh dikirim.

Image:

- `title` wajib.
- `description` wajib.
- `type` harus `image`.
- `file` wajib.
- MIME harus `image/jpeg` atau `image/png`.
- `youtube_url` tidak boleh dikirim.

YouTube:

- `title` wajib.
- `description` wajib.
- `type` harus `youtube`.
- `youtube_url` wajib dan harus URL valid.
- `file` tidak boleh dikirim.

Ukuran file maksimum:

```text
10 MB
```

## Database

### users

Tabel `users` memakai struktur bawaan Laravel, dengan tambahan kolom `role`.

| Field | Tipe | Catatan |
| --- | --- | --- |
| id | bigint | Primary key |
| name | varchar | Nama user |
| email | varchar | Email user |
| password | varchar | Hash password |
| role | enum | `admin` atau `siswa` |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

### file_materis

| Field | Tipe | Catatan |
| --- | --- | --- |
| id | bigint | Primary key |
| title | varchar | Judul soal |
| description | text | Deskripsi soal |
| type | enum | `pdf`, `image`, `youtube` |
| file_path | varchar nullable | Dipakai untuk PDF/image |
| youtube_url | varchar nullable | Dipakai untuk YouTube |
| created_by | bigint | Foreign key ke `users.id` |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

Relasi:

```text
users.id -> file_materis.created_by
```

Satu admin bisa membuat banyak materi. Materi tetap menyimpan `created_by` agar asal data jelas.

## Arsitektur kode

Bagian backend dipisah agar logic tidak menumpuk di controller.

```text
app/
  Data/
    AuthTokenData.php
    DownloadableMateriData.php
    MateriCreateData.php
    MateriData.php
    MateriListData.php
    MateriUpdateData.php
  Enums/
    MateriType.php
  Http/
    Controllers/Api/
      AdminMateriController.php
      AuthController.php
      SiswaMateriController.php
    Middleware/
      EnsureRole.php
    Requests/
      LoginRequest.php
      StoreMateriRequest.php
      UpdateMateriRequest.php
    Resources/
      AuthTokenResource.php
      MateriCollection.php
      MateriResource.php
  Models/
    FileMateri.php
    User.php
  Policies/
    MateriPolicy.php
  Services/
    AuthService.php
    MateriService.php
```

Pembagian tanggung jawab:

- Controller menerima request dan mengembalikan resource.
- Form Request mengurus validasi.
- Service mengurus proses bisnis, storage, cache, dan transaksi database.
- DTO di folder `Data` membawa data antar layer.
- Policy membatasi akses berdasarkan role.
- Resource merapikan response API.

## Caching

Endpoint list materi memakai cache:

```text
materi.list.{role}.{page}
```

Contoh:

```text
materi.list.admin.1
materi.list.siswa.1
```

TTL:

```text
300 detik
```

Cache hanya dipakai untuk:

```http
GET /api/list-materi
```

Cache dihapus saat:

- Materi dibuat.
- Materi diubah.
- Materi dihapus.

Project memakai file cache driver, jadi invalidation dilakukan memakai registry key dan `Cache::forget()`. Tidak memakai cache tags karena file driver Laravel tidak mendukung tags.

## Storage

File PDF dan image disimpan di disk `public`.

Path penyimpanan:

```text
storage/app/public/materis/pdf
storage/app/public/materis/image
```

URL akses:

```text
/storage/materis/pdf/{filename}
/storage/materis/image/{filename}
```

Jika preview PDF atau image tidak muncul, cek tiga hal ini:

1. Sudah menjalankan `php artisan storage:link`.
2. File benar-benar ada di `storage/app/public`.
3. Aplikasi dibuka dari host yang sama dengan URL file. Project ini memakai `file_url` relative (`/storage/...`) untuk menghindari masalah `APP_URL` yang tidak sama dengan host browser.

## UI

UI dibuat dengan Blade, Alpine.js, Tailwind CSS, dan SweetAlert2.

Komponen yang dipakai bersama:

```text
resources/views/components/dashboard/navbar.blade.php
resources/views/components/dashboard/header.blade.php
resources/views/components/layouts/app.blade.php
```

Halaman admin:

- Daftar materi dengan tabel desktop dan card list untuk mobile/tablet.
- Form create dan edit memakai layout yang sama.
- Tombol delete memakai SweetAlert2.
- Tombol edit/download/copy/delete memakai icon.
- Tombol update di halaman edit disabled sampai ada perubahan data.

Halaman siswa:

- Daftar materi memakai card responsive.
- Detail materi memakai viewer berbeda untuk PDF, image, dan YouTube.
- PDF dan image punya fallback berupa tombol buka tab baru dan download.

## Build dan verifikasi

Build asset:

```bash
npm run build
```

Compile Blade:

```bash
php artisan view:cache
php artisan view:clear
```

Jalankan test:

```bash
php artisan test
```

Catatan: test bawaan Laravel `ExampleTest` biasanya mengecek `/` harus `200`. Di project ini `/` memang redirect ke `/login`, jadi test bawaan itu perlu disesuaikan jika ingin dipakai sebagai test final.

## Catatan pengerjaan

- Register publik tidak dibuat karena technical test hanya membutuhkan role Admin dan Siswa. Akun dibuat lewat seeder.
- API memakai Sanctum token dan role ability (`admin` atau `siswa`).
- Endpoint list dipakai bersama admin dan siswa, tetapi akses tetap dibatasi middleware role dan policy.
- YouTube URL diubah menjadi embed URL di `MateriData`.
- File URL dibuat dalam format relative `/storage/...` agar preview tetap jalan di local server, Laragon vhost, atau host development lain.
