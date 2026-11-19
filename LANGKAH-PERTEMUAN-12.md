# Langkah Pembuatan — Pertemuan 12

**Relasi Database KursusKu: Course – Participant – Enrollment**

Lanjutan project pertemuan 11 (Course CRUD lengkap). Jangan membuat project baru. Urutan wajib:

`ERD → Migration → Foreign Key → Model Relation → Form → Store → Report → Cascade Test`

> Gunakan database lokal Laragon dan **data dummy**. Jangan menguji cascade pada data penting.

---

## Penyesuaian terhadap PDF panduan

Beberapa isi PDF disesuaikan dengan kondisi project KursusKu Anda. Ini penting agar tidak error saat praktik.

| PDF panduan | Di project ini | Alasan |
| --- | --- | --- |
| Dropdown memfilter course `status = 'active'` | `status = 'open'` | Status course di KursusKu: `draft`, `open`, `closed`. Nilai `active` tidak pernah ada, sehingga dropdown akan kosong. |
| Course dummy cascade `status => 'active'` | `status => 'open'` | Alasan sama. Nilai `active` juga ditolak aturan validasi Course. |
| Route `Route::resource('enrollments', ...)` | Route dummy minggu 9 dibersihkan | Project sudah punya route bernama `enrollments.create` dan `enrollments.preview` (form dummy minggu 9). Nama `enrollments.create` akan bentrok dengan resource. |
| View form polos (`<h1>`, `<form>`) | Bootstrap + `@extends('layouts.app')` + `@error` | Konsisten dengan layout minggu 9–11. |
| Ajax sisa kursi memakai data dummy | Dihitung dari database | ID course kini ID asli database. `remaining = quota − jumlah enrollment`. |
| `start_date` tampil mentah (opsional di PDF) | `format('d-m-Y')` | `Course` sudah punya cast `date` sejak pertemuan 10. |

Yang **tidak** diubah: struktur tabel, nama kolom, aturan unique, cascade, relasi Eloquent, dan alur laporan — sama dengan PDF.

---

## Langkah 0 — Persiapan

1. Laragon → **Start All** (MySQL aktif).
2. Terminal VS Code di folder project:

```bash
php artisan --version
php artisan migrate:status
php artisan route:list
```

3. Pastikan `/courses` masih jalan dan ada beberapa course.
4. Pastikan minimal 1–2 course berstatus **open** (ubah lewat menu Kursus → Edit jika perlu). Hanya course `open` muncul di form enrollment.
5. Titik aman Git:

```bash
git add .
git commit -m "checkpoint before meeting 12 relations"
```

**Jangan lanjut** jika MySQL mati, migration courses belum sukses, halaman Course error, atau nama database di `.env` salah.

Pasang file dari zip: ekstrak ke folder project, pilih **Replace**.

---

## Langkah 1 — ERD final

Buka `evidence/week-12/erd-final.svg` di browser (versi Mermaid ada di `erd.md`). Pelajari lalu gambar ulang/ikuti sebagai ERD Anda.

- `courses` 1 → banyak `enrollments`
- `participants` 1 → banyak `enrollments`
- `enrollments`: PK `id`, FK `participant_id`, FK `course_id`, `status`, **UQ(participant_id, course_id)**

Jawab checkpoint dosen di `erd.md`: Alya 3 kursus = **3 baris**; 20 peserta Laravel Dasar = **20 baris**; nama course tidak disimpan di enrollment karena dibaca lewat relasi (tidak ada duplikasi).

Screenshot → `evidence/week-12/01-erd.png`.

---

## Langkah 2 — Migration

Dua file sudah disediakan di zip:

- `database/migrations/2026_11_19_000001_create_participants_table.php`
- `database/migrations/2026_11_19_000002_create_enrollments_table.php`

Penting:
- **Jangan** menjalankan `make:migration` lagi untuk keduanya, karena akan menjadi migration ganda dan error "Table already exists". Jika sudah terlanjur membuatnya sendiri, hapus file buatan Anda dan pakai file dari zip.
- Urutan harus `participants` → `enrollments` (nama file berurutan), karena enrollments mereferensikan participants dan courses.

Inti `enrollments`:

```php
$table->foreignId('participant_id')->constrained()->cascadeOnDelete();
$table->foreignId('course_id')->constrained()->cascadeOnDelete();
$table->string('status', 20)->default('registered');
$table->unique(['participant_id', 'course_id']);
```

- `constrained()` → `participant_id` mengarah ke `participants.id`, `course_id` ke `courses.id`.
- `cascadeOnDelete()` → hapus induk, enrollment ikut terhapus (entity lain tidak).
- `unique([...])` → **pasangan** participant+course unik. Bukan unique per kolom!

---

## Langkah 3 — Jalankan migration + cek foreign key

```bash
php artisan migrate
php artisan migrate:status
```

Dua migration baru harus berstatus **Ran/DONE**. Lalu di phpMyAdmin/HeidiSQL (database `kursusku-laravel`), tabel `enrollments` → Structure/Foreign keys, atau SQL:

```sql
SHOW CREATE TABLE enrollments;
```

Cari: dua `FOREIGN KEY`, `ON DELETE CASCADE`, dan unique index gabungan.

Evidence: `02-migrate.png`, `03-foreign-key.png`.

| Gejala | Penyebab | Solusi |
| --- | --- | --- |
| Cannot add foreign key | tabel target belum ada / tipe id beda | pastikan `courses` dan `participants` sudah dibuat dulu |
| Table already exists | migration pernah sebagian berjalan | cek `migrate:status`, rollback seperlunya |
| Connection refused | MySQL belum aktif | Start All, cek `.env` |

---

## Langkah 4 — Model dan relasi Eloquent

File di zip: `Participant.php` (baru), `Enrollment.php` (baru), `Course.php` (ditambah relasi; `$fillable`, `casts()`, dan `HasFactory` dari pertemuan sebelumnya tetap).

| Model | Relasi |
| --- | --- |
| Course | `enrollments()` hasMany, `participants()` belongsToMany via `enrollments` |
| Participant | `enrollments()` hasMany, `courses()` belongsToMany via `enrollments` |
| Enrollment | `participant()` belongsTo, `course()` belongsTo |

Uji di Tinker:

```php
php artisan tinker
>>> new App\Models\Participant;
>>> App\Models\Course::first()?->enrollments;
>>> App\Models\Participant::first()?->enrollments;
```

Untuk pemula: fokus ke `enrollments()`. `courses()` / `participants()` hanya jalan pintas membaca M:N.

---

## Langkah 5 — Participant dummy

Cara cepat (aman diulang, tidak membuat data ganda):

```bash
php artisan db:seed --class=ParticipantSeeder
```

Atau lewat Tinker (jika diminta dosen, **jangan jalankan keduanya** karena email unik):

```php
use App\Models\Participant;
Participant::create(['name' => 'Alya Rahma',   'email' => 'alya@example.test',  'phone' => '081200000001']);
Participant::create(['name' => 'Bima Putra',   'email' => 'bima@example.test',  'phone' => '081200000002']);
Participant::create(['name' => 'Citra Nabila', 'email' => 'citra@example.test', 'phone' => null]);
```

Periksa:

```php
>>> Participant::all(['id','name','email']);
>>> App\Models\Course::all(['id','code','name','start_date','status']);
```

Indikator berhasil: tiga participant dengan id berbeda. Email memakai `example.test` karena bukan email asli.

---

## Langkah 6 — EnrollmentController

File: `app/Http/Controllers/EnrollmentController.php` (tiga method):

- `create()` → mengambil participant (urut nama) dan course `open`.
- `store()` → validasi `exists` + `Rule::unique` pada **pasangan**, status otomatis `registered`.
- `index()` → `Enrollment::with(['participant','course'])->latest()->get()` (eager loading).

Pesan error bahasa Indonesia sudah disertakan. Untuk duplikat: *"Participant ini sudah terdaftar pada course tersebut."*

Dua lapis proteksi duplikasi: (1) validasi Laravel memberi pesan ramah, (2) unique index database menjamin integritas walau ada request bersamaan.

---

## Langkah 7 — Route dan form

`routes/web.php` (sudah diperbarui di zip):

```php
Route::redirect('/pendaftaran', '/enrollments/create');   // URL lama minggu 9 tetap hidup
Route::get('/courses/{course}/availability', [CourseController::class, 'availability'])->name('courses.availability');
Route::resource('courses', CourseController::class);
Route::resource('enrollments', EnrollmentController::class)->only(['index', 'create', 'store']);
```

Route dummy `enrollments.preview` dan closure availability lama dihapus (tergantikan). Cek:

```bash
php artisan route:list --name=enrollments
php artisan route:list --name=courses
```

Harus ada `enrollments.index`, `enrollments.create`, `enrollments.store`, dan 7 route `courses.*` + `courses.availability`.

View `resources/views/enrollments/create.blade.php`: dua dropdown relasional, `@error` per field, `old()` agar pilihan tetap setelah gagal, `novalidate` agar validasi server yang diuji, dan kotak `#availability` untuk jQuery Ajax (`resources/js/app.js` tidak diubah).

Status tidak dipilih user; controller menetapkan `registered`.

Jalankan `npm run dev` (Ajax butuh Vite).

---

## Langkah 8 — Uji validasi duplikasi

1. Alya → Laravel Dasar: **berhasil**
2. Alya → Laravel Dasar lagi: **ditolak**
3. Alya → PHP Dasar: **boleh**
4. Bima → Laravel Dasar: **boleh**

> Kesalahan logika umum: membuat `participant_id` atau `course_id` unique sendiri-sendiri. Itu membuat satu participant hanya bisa ikut satu course (atau sebaliknya).

Screenshot pesan duplikat → `05-duplicate.png`.

---

## Langkah 9 — Laporan relasional

Buka `/enrollments`: participant (+ email), course (+ kode), start date (`d-m-Y`), dan status. Data diambil lewat relasi; **tabel enrollments hanya menyimpan id**.

Tambahan di halaman detail course (`/courses/{id}`): daftar peserta terdaftar dan "X dari Y kursi".

Screenshot → `04-form-enrollment.png`, `06-laporan.png`.

---

## Langkah 10 — End-to-end test (manual)

Isi `evidence/week-12/test-matrix.md` bagian A (8 skenario). Lalu cek database, bukan hanya browser:

```sql
SELECT id, participant_id, course_id, status
FROM enrollments
ORDER BY id DESC;
```

Cocokkan `participant_id` dan `course_id` dengan id di tabel master. Screenshot → `07-db-enrollments.png`.

Pertanyaan dosen: dari mana `participant->name` dan `course->start_date` berasal? Mengapa enrollment menyimpan id, bukan nama?

---

## Langkah 11 — Cascade test A: hapus Course dummy

Skrip lengkap di `evidence/week-12/cascade-test.md` (status dummy sudah `open`). Intinya:

```php
Enrollment::where('course_id', $course->id)->count();   // 1
$course->delete();
Enrollment::where('course_id', $course->id)->count();   // 0
Participant::find($participant->id) !== null;            // true
```

Makna: enrollment ikut hilang (FK cascade), **participant tetap ada**. Screenshot → `08-cascade-course.png`.

## Langkah 12 — Cascade test B: hapus Participant dummy

```php
Enrollment::where('participant_id', $participant2->id)->count();  // 1
$participant2->delete();
Enrollment::where('participant_id', $participant2->id)->count();  // 0
Course::find($course2->id) !== null;                               // true
```

Makna: enrollment ikut hilang, **course tetap ada**. Bersihkan sisa dummy (`$course2->delete(); $participant->delete();`). Screenshot → `09-cascade-participant.png`.

> Perhatian: menghapus Course lewat tombol **Hapus** di halaman Kursus kini ikut menghapus semua enrollment course itu (pesan konfirmasi sudah diberi peringatan). Jangan menghapus course yang sudah dipakai data nyata.

---

## Langkah 13 — Tes otomatis

```bash
php artisan test --filter=EnrollmentTest
php artisan test
```

`tests/Feature/EnrollmentTest.php` mencakup: relasi dua arah, 8 skenario matrix, exists, unique index database, email unik, cascade course & participant, Ajax availability, detail course, redirect `/pendaftaran`. Tes memakai SQLite in-memory (butuh ekstensi `pdo_sqlite`, umumnya aktif di Laragon). Screenshot hasil → `10-test-result.png`.

---

## Langkah 14 — Troubleshooting

Urutan: error message → route → controller → model → migration/DB.

| Masalah | Kemungkinan penyebab | Perbaikan |
| --- | --- | --- |
| Class Participant not found | namespace/import | `use App\Models\Participant;` |
| Unknown column participant_id | migration belum jalan | `php artisan migrate` |
| FK constraint fails saat insert | id tidak ada | pakai dropdown + rule `exists` |
| Duplicate entry | pasangan sudah ada | pastikan `Rule::unique` + pesan error |
| Attempt to read property "name" on null | data orphan / FK salah | cek constraint di DB |
| Course tidak tampil di dropdown | status bukan `open` | ubah status course ke `open` |
| Delete course gagal | FK bukan cascade / migration lama | `SHOW CREATE TABLE enrollments` |
| Route [enrollments.preview] not defined | masih ada view lama | pastikan `create.blade.php` dari zip menimpa file lama |
| Sisa kursi tidak berubah | Vite mati | `npm run dev`, cek Console browser |

Perintah bantu: `php artisan route:list`, `php artisan migrate:status`, `php artisan optimize:clear`, `php artisan tinker`.

---

## Langkah 15 — Evidence, commit, dan checklist

```
evidence/week-12/
  01-erd.png            02-migrate.png         03-foreign-key.png
  04-form-enrollment.png 05-duplicate.png      06-laporan.png
  07-db-enrollments.png  08-cascade-course.png 09-cascade-participant.png
  10-test-result.png
  erd.md / erd-final.svg  test-matrix.md  cascade-test.md  ai-usage-log.md
```

Commit:

```bash
git add .
git commit -m "feat: add participant course enrollment relations"
```

Checklist:

- [ ] ERD final benar
- [ ] `participants` dan `enrollments` berhasil dibuat
- [ ] Dua foreign key aktif + composite unique aktif
- [ ] `Participant->enrollments`, `Enrollment->participant`, `Enrollment->course`, `Course->enrollments`
- [ ] Form dua dropdown bekerja, enrollment tersimpan, duplikat ditolak
- [ ] Laporan relasional tampil
- [ ] 8 test matrix dijalankan
- [ ] Cascade course dan participant diuji dan bisa dijelaskan
- [ ] AI usage log (jika memakai AI)

## Refleksi

1. Apa fungsi foreign key dibanding sekadar menyimpan angka id?
2. Mengapa unique harus pada pasangan participant-course?
3. Mengapa `start_date` tidak disimpan ulang pada `enrollments`?
4. Apa yang terjadi jika cascade tidak dipasang?
5. Apa perbedaan `belongsTo()` dan `hasMany()`?

---

## Daftar file di zip ini

| File | Status |
| --- | --- |
| `database/migrations/2026_11_19_000001_create_participants_table.php` | baru |
| `database/migrations/2026_11_19_000002_create_enrollments_table.php` | baru |
| `app/Models/Participant.php`, `Enrollment.php` | baru |
| `app/Models/Course.php` | diubah (relasi) |
| `app/Http/Controllers/EnrollmentController.php` | baru |
| `app/Http/Controllers/CourseController.php` | diubah (`show` memuat peserta, `availability`) |
| `routes/web.php` | diubah (route enrollment, dummy minggu 9 dibersihkan) |
| `resources/views/enrollments/create.blade.php` | diganti (form dummy → form relasional) |
| `resources/views/enrollments/index.blade.php` | baru (laporan) |
| `resources/views/courses/show.blade.php` | diubah (daftar peserta) |
| `resources/views/courses/index.blade.php` | diubah (konfirmasi hapus menyebut cascade) |
| `resources/views/partials/navbar.blade.php` | diubah (menu Laporan Enrollment) |
| `database/factories/ParticipantFactory.php`, `EnrollmentFactory.php` | baru |
| `database/seeders/ParticipantSeeder.php` | baru |
| `tests/Feature/EnrollmentTest.php` | baru |
| `evidence/week-12/*` | baru (ERD, template matrix, cascade, log AI) |
| `README.md`, `LANGKAH-PEMBUATAN.md` | diperbarui |

Setelah ekstrak: `php artisan migrate` lalu `php artisan db:seed --class=ParticipantSeeder`.
