# KursusKu

Project praktikum Pemrograman Web III — **Pertemuan 11**.

Ini lanjutan project pertemuan 7, bukan project baru dari nol.
Riwayat Git masih menyimpan landing page minggu 7, lalu commit UI + Ajax minggu 9.

## Requirement

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- Laravel 13
- Laragon / web server lokal

## Cara Menjalankan

Butuh **dua terminal**.

Terminal 1:

1. Buka folder project.
2. `composer install`
3. Salin `.env.example` menjadi `.env` jika belum ada.
4. `php artisan key:generate`
5. `php artisan serve`

Terminal 2:

1. `npm install`
2. `npm run dev`

Biarkan `npm run dev` tetap jalan. Buka alamat `php artisan serve` (umumnya `http://127.0.0.1:8000`).

Periksa route:

```bash
php artisan route:list
```

Wajib ada nama: `home`, `enrollments.create`, `enrollments.preview`, `courses.availability`, dan 7 route `courses.*` (index, create, store, show, edit, update, destroy).

## Fitur Milestone 9

- Master layout `resources/views/layouts/app.blade.php`
- Navbar partial `resources/views/partials/navbar.blade.php`
- Home memakai `@extends`, tetap menampilkan data `$featured` dari `HomeController`
- Form pendaftaran Blade + `@csrf` + validasi
- Route JSON dummy `GET /courses/{id}/availability`
- jQuery Ajax mengubah sisa kursi tanpa reload
- Bootstrap dan Popper lewat Vite, bukan CDN

## Fitur Milestone 11 — Course CRUD Lengkap

- Resource `courses` lengkap: index, create, store, show, edit, update, destroy
- Validasi di `StoreCourseRequest` dan `UpdateCourseRequest` (unique update memakai `ignore`)
- Form create/edit berbagi partial `courses/_form.blade.php`, memakai `old()` dan `@error` per field
- Delete memakai `@csrf` + `@method('DELETE')` + konfirmasi
- Test matrix 15 kasus: `php artisan test --filter=CourseCrudTest`

Langkah rinci: [LANGKAH-PERTEMUAN-11.md](LANGKAH-PERTEMUAN-11.md).

## Belum Dikerjakan

- Relasi Course - Participant - Enrollment
- Authentication
- Ajax POST

## Uji cepat

1. Beranda: navbar Bootstrap tampil.
2. Perkecil browser: tombol hamburger membuka menu.
3. Pendaftaran: form tampil di layout yang sama.
4. Pilih Web Dasar → `12 kursi tersisa untuk Web Dasar.`
5. Pilih PHP Dasar → `8 kursi tersisa`.
6. Pilih Laravel Dasar → `4 kursi tersisa`.
7. Ketik nama, lalu ganti kursus: nama tidak hilang (halaman tidak reload).
8. Submit nama minimal 3 huruf dan email valid → alert sukses.
9. Submit email salah → pesan validasi.
10. DevTools → Network → request availability status 200 + JSON.

Langkah berurutan: [LANGKAH-PERTEMUAN-9.md](LANGKAH-PERTEMUAN-9.md).
Langkah minggu 7 (jika perlu diulang): [LANGKAH-PERTEMUAN-7.md](LANGKAH-PERTEMUAN-7.md).
