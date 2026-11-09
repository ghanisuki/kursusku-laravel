# Langkah Pembuatan — Pertemuan 11

**Laravel CRUD Lanjutan: Show, Validation, Edit, Update & Delete**

Lanjutan project pertemuan 10 (CRUD tahap 1: index, create, store). Jangan membuat project baru. Kerjakan berurutan; jangan lompat sebelum checkpoint tahap sebelumnya benar.

Ringkasan alur: `Browser → Route → Controller → Form Request → Model → MySQL`

---

## Langkah 0 — Pastikan titik awal siap

1. Jalankan Laragon → **Start All** (MySQL aktif).
2. Buka project di VS Code, lalu terminal:

```bash
cd C:\laragon\www\kursusku-laravel
php artisan --version
php artisan migrate:status
```

3. Jalankan aplikasi (dua terminal):

```bash
php artisan serve
npm run dev
```

4. Buka `/courses`. Pastikan daftar dan form tambah masih berfungsi.

**Titik aman Git (disarankan):**

```bash
git add .
git commit -m "checkpoint before course crud advanced"
```

> Catatan: pada zip sebelumnya `index.blade.php` berisi dua dokumen HTML yang tergabung (daftar + form tambah) dan `create.blade.php` masih placeholder. Zip pertemuan 11 memperbaikinya (Langkah 3 dan 6).

---

## Langkah 1 — Route resource lengkap

`routes/web.php`: ganti baris resource terakhir.

```php
// sebelum
Route::resource('courses', CourseController::class)->only(['index', 'create', 'store']);

// sesudah
Route::resource('courses', CourseController::class);
```

Uji:

```bash
php artisan route:list --name=courses
```

Harus ada 7 route: `index, create, store, show, edit, update, destroy`.
Route `courses.availability` (Ajax minggu 9) tetap ada karena path-nya berbeda (`/courses/{course}/availability`).

**Checkpoint 1:** 7 route `courses.*` tampil. Screenshot → `evidence/week-11/01-route-list.png`.

---

## Langkah 2 — Show + route model binding

`app/Http/Controllers/CourseController.php`

```php
public function show(Course $course)
{
    return view('courses.show', compact('course'));
}
```

`Course $course` membuat Laravel mencari record dari ID di URL secara otomatis. ID tidak ada → **404**.

Buat `resources/views/courses/show.blade.php` (sudah tersedia di zip, memakai `@extends('layouts.app')`).

> Layout `layouts/app.blade.php` sudah menampilkan `session('success')`, jadi view show/edit **tidak perlu** menambah alert sendiri (supaya pesan tidak dobel).

**Checkpoint 2:** buka `/courses/1`: kode, nama, deskripsi, biaya, kuota, tanggal, status sesuai database. Buka `/courses/999999` → 404. Screenshot → `02-course-show.png`.

---

## Langkah 3 — Tombol aksi di index

`resources/views/courses/index.blade.php` sekarang memakai layout dan punya kolom **Aksi**: Detail, Edit, Hapus.

Hapus memakai **form POST + `@csrf` + `@method('DELETE')`**, bukan link biasa, karena menghapus mengubah data.

**Uji:** Detail → halaman show. Edit → error `View [courses.edit] not found` (normal, view belum dibuat). Jangan uji Hapus dulu.

---

## Langkah 4 — Buat Form Request

```bash
php artisan make:request StoreCourseRequest
php artisan make:request UpdateCourseRequest
```

Dua file muncul di `app/Http/Requests/`. Pada keduanya pastikan:

```php
public function authorize(): bool
{
    return true;   // jika false -> 403 Forbidden walau data valid
}
```

Mengapa dua request? **Store**: code harus unik terhadap semua Course. **Update**: code boleh sama dengan milik record yang sedang diedit.

---

## Langkah 5 — Isi StoreCourseRequest

```php
public function rules(): array
{
    return [
        'code'        => ['required', 'string', 'max:20', 'unique:courses,code'],
        'name'        => ['required', 'string', 'min:3', 'max:120'],
        'description' => ['nullable', 'string', 'max:2000'],
        'fee'         => ['required', 'integer', 'min:0'],
        'quota'       => ['required', 'integer', 'between:1,500'],
        'start_date'  => ['required', 'date'],
        'status'      => ['required', 'in:draft,open,closed'],
    ];
}
```

> Perhatikan: `start_date` sekarang **wajib** (pada tahap 1 masih nullable), sesuai panduan pertemuan 11.

---

## Langkah 6 — Isi UpdateCourseRequest (unique yang benar)

```php
use Illuminate\Validation\Rule;

public function rules(): array
{
    $course = $this->route('course');

    return [
        'code' => [
            'required', 'string', 'max:20',
            Rule::unique('courses', 'code')->ignore($course),
        ],
        // name, description, fee, quota, start_date, status: sama seperti Store
    ];
}
```

Kalimat kunci: *"Code harus unik terhadap Course lain, tetapi boleh sama dengan code milik Course yang sedang saya edit."*

Kesalahan umum: memakai `unique:courses,code` saja pada update → code milik sendiri ditolak.

---

## Langkah 7 — Refactor controller

Import dulu:

```php
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
```

Lalu:

```php
public function store(StoreCourseRequest $request)
{
    Course::create($request->validated());

    return to_route('courses.index')->with('success', 'Kursus berhasil ditambahkan.');
}

public function edit(Course $course)
{
    return view('courses.edit', compact('course'));
}

public function update(UpdateCourseRequest $request, Course $course)
{
    $course->update($request->validated());

    return to_route('courses.show', $course)->with('success', 'Kursus berhasil diperbarui.');
}

public function destroy(Course $course)
{
    $course->delete();

    return to_route('courses.index')->with('success', 'Kursus berhasil dihapus.');
}
```

`validated()` hanya berisi data yang lolos rules. Aturan tidak lagi ditulis di controller.

---

## Langkah 8 — Form create dan edit

Form create dan edit hampir sama, jadi field-nya ditaruh di satu partial: `resources/views/courses/_form.blade.php`.

- Create: `@include('courses._form', ['course' => null])`
- Edit: `@include('courses._form', ['course' => $course])` + `@method('PUT')`

Pola setiap field:

```blade
<input type="text" id="name" name="name"
       value="{{ old('name', $course?->name) }}"
       class="form-control @error('name') is-invalid @enderror">
@error('name')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
```

- `old('name', nilaiDatabase)`: jika validasi gagal, input terakhir user tetap tampil; jika tidak, nilai database.
- Tanggal untuk `<input type="date">` harus `format('Y-m-d')`.
- Form memakai `novalidate` agar validasi HTML browser (min/max) tidak menutupi validasi server. Dengan begitu kasus quota 0 dan 501 benar-benar diuji di server.

**Checkpoint 3:** buka `/courses/1/edit`: semua field terisi data database. Ubah nama saja → Simpan → diarahkan ke detail + alert sukses. Screenshot → `03-course-edit.png`, `06-update-success.png`.

---

## Langkah 9 — Destroy

Sudah ada di controller (Langkah 7). Uji dengan Course dummy:

1. Buat Course `DEL-001`.
2. Klik **Hapus** → **Cancel** → data tetap ada.
3. Klik **Hapus** → **OK** → data hilang + alert "Kursus berhasil dihapus."

Screenshot → `07-delete-success.png`.

> Pertemuan berikutnya akan ada relasi Enrollment; kebijakan hapus Course yang sudah dipakai pendaftaran harus dipikirkan lagi.

---

## Langkah 10 — Error dekat field

Semua 7 field (`code, name, description, fee, quota, start_date, status`) memakai pola `@error` + `is-invalid` seperti Langkah 8, sehingga pesan muncul tepat di bawah field yang salah.

Screenshot → `04-validation-duplicate-code.png`, `05-validation-quota-501.png`.

---

## Langkah 11 — Pengujian (minimal 12 kasus)

### A. Manual di browser
Isi `evidence/week-11/test-matrix.md`. Urutan aman: data valid → detail → edit tanpa ubah code → error satu per satu → boundary quota (0 → 1 → 500 → 501) → status invalid → **delete paling akhir**.

Kasus status invalid (`active`): select biasa tidak menyediakannya. Buka DevTools → ubah `value` salah satu `<option>` menjadi `active` → submit → harus ditolak. Ini membuktikan server tidak mempercayai UI.

### B. Otomatis
```bash
php artisan test --filter=CourseCrudTest
```

File `tests/Feature/CourseCrudTest.php` mencakup 15 kasus test matrix, ditambah show/404, nilai edit, dan old input. Tes memakai SQLite in-memory (lihat `phpunit.xml`); butuh ekstensi `pdo_sqlite` aktif (umumnya sudah aktif di Laragon).

Hasil `php artisan test` → screenshot `08-test-result.png`.

---

## Langkah 12 — Troubleshooting cepat

| Gejala | Penyebab | Perbaikan |
| --- | --- | --- |
| 403 setelah submit | `authorize()` false | `return true;` |
| 419 Page Expired | tidak ada `@csrf` | tambahkan `@csrf` |
| Method Not Allowed (update) | tidak ada `@method('PUT')` | tambahkan |
| Method Not Allowed (delete) | tidak ada `@method('DELETE')` | tambahkan |
| Edit dengan code sendiri ditolak | unique tanpa `ignore` | `Rule::unique(...)->ignore($course)` |
| Tanggal edit kosong | format salah | `format('Y-m-d')` |
| Data valid tidak tersimpan | tidak ada di `$fillable` | cek model `Course` |
| Input hilang setelah error | tidak memakai `old()` | `old('field', nilaiSaatIni)` |
| `View [layouts.app] ...` / halaman tanpa gaya | `npm run dev` mati | jalankan `npm run dev` |

Urutan debugging: pesan error → file/baris → route → rules → controller → view → database.

---

## Langkah 13 — Evidence dan checklist akhir

Folder `evidence/week-11/`:

```
01-route-list.png
02-course-show.png
03-course-edit.png
04-validation-duplicate-code.png
05-validation-quota-501.png
06-update-success.png
07-delete-success.png
08-test-result.png
test-matrix.md        (atau .pdf / .xlsx)
ai-usage-log.md
```

Checklist:

- [ ] Route resource Course lengkap (7 route)
- [ ] Halaman detail dapat dibuka
- [ ] `StoreCourseRequest` dan `UpdateCourseRequest` ada, `authorize()` true
- [ ] Store dan update memakai `$request->validated()`
- [ ] Unique update mengabaikan record sendiri
- [ ] Edit memakai `old(field, nilaiSaatIni)`
- [ ] Error tampil dekat field
- [ ] Update valid berhasil, flash sukses tampil
- [ ] Delete memakai CSRF + `DELETE` + konfirmasi
- [ ] Minimal 12 test case tercatat + screenshot
- [ ] Saya dapat menjelaskan alur show, update, delete tanpa melihat AI

## Refleksi (jawab di `test-matrix.md`)

1. Mengapa validasi dipindahkan dari controller ke Form Request?
2. Mengapa unique rule pada update berbeda dari store?
3. Apa fungsi `@method('PUT')` dan `@method('DELETE')`?

---

## Daftar file di zip ini

| File | Status |
| --- | --- |
| `routes/web.php` | diubah (resource lengkap) |
| `app/Http/Controllers/CourseController.php` | diubah (Form Request, show, edit, update, destroy) |
| `app/Http/Requests/StoreCourseRequest.php` | baru |
| `app/Http/Requests/UpdateCourseRequest.php` | baru |
| `app/Models/Course.php` | diubah (`HasFactory`) |
| `database/factories/CourseFactory.php` | baru |
| `resources/views/courses/index.blade.php` | diganti (layout + tombol aksi; bug dokumen ganda diperbaiki) |
| `resources/views/courses/create.blade.php` | diganti (sebelumnya placeholder) |
| `resources/views/courses/_form.blade.php` | baru (field bersama) |
| `resources/views/courses/show.blade.php` | baru |
| `resources/views/courses/edit.blade.php` | baru |
| `resources/views/partials/navbar.blade.php` | diubah (menu Kursus) |
| `tests/Feature/CourseCrudTest.php` | baru |
| `evidence/week-11/*` | baru (template test matrix + log AI) |
| `README.md`, `LANGKAH-PEMBUATAN.md` | diperbarui |
