# Langkah Pembuatan — Pertemuan 13

**Pagination, Searching, Filter Status, Ajax Live Search & Draft Artikel Ilmiah**

Lanjutan project pertemuan 12. Jangan membuat project baru. Urutan kerja:

`Data dummy → Search q → Filter status → Pagination → Empty state → Uji tanpa JavaScript → (Opsional) Ajax → Uji 5 skenario → Draft artikel`

> Aturan kelas: **baseline server-side (form GET) harus selesai dan lulus uji lebih dulu.** Ajax hanya peningkatan kenyamanan dan tidak boleh merusak fitur wajib.

---

## Penyesuaian terhadap PDF panduan

| PDF panduan | Di project ini | Alasan |
| --- | --- | --- |
| `cd C:\laragon\www\kursusku`, URL `kursusku.test` | Folder `kursusku-laravel`, alamat `http://127.0.0.1:8000` | Sesuai proyek dan cara menjalankan Anda (`php artisan serve`). |
| Tabel hasil 3 kolom (kode, nama, status) | 7 kolom + tombol Detail/Edit/Hapus | Tabel yang sudah ada dari pertemuan 11 dipertahankan, jadi CRUD tidak hilang dari halaman daftar. |
| `{{ $courses->links() }}` saja | Ditambah `Paginator::useBootstrapFive()` di `AppServiceProvider` | Tanpa ini Laravel menampilkan pagination gaya Tailwind yang tampil berantakan di proyek Bootstrap. |
| `orderBy('name')` | `orderBy('name')->orderBy('id')` | Pembeda kedua membuat urutan antar halaman stabil bila ada nama kembar. |
| Link pagination pada respons partial | `appends(['partial' => null])` | Agar link di fragmen hasil tidak membawa `partial=1`. |
| Skrip jQuery di Blade (`route(...)` di dalam JS) | Ditaruh di `resources/js/app.js`, alamat diambil dari `action` form | jQuery sudah dimuat lewat Vite sejak pertemuan 9. Tidak ada JS inline. |
| Ajax sederhana | + batalkan request lama, efek loading, pesan gagal, `replaceState` | Mencegah hasil lama menimpa hasil baru dan membuat URL tetap sesuai hasil. |
| Data dummy "siapkan sendiri" | `CoursePaginationSeeder` (24 data) | Praktis dan bisa diulang. |
| Dashboard count (tambahan proyek) | Kartu ringkasan di beranda | Total Course, Course Open, Participant, Enrollment. |
| `q[]` / nilai aneh pada URL tidak dibahas | Dianggap kosong, tidak error | Aplikasi tidak boleh error 500 karena URL diubah-ubah. |

Yang **tidak** diubah: skema database, nama kolom, aturan validasi, status hanya `open/closed/draft`, `paginate(10)`, `withQueryString()`, fallback GET.

---

## Langkah 0 — Persiapan dan checkpoint

1. Laragon → **Start All**.
2. Di Cmder, masuk ke folder project dan cek kondisi awal:

```bash
cd C:\laragon\www\kursusku-laravel
php artisan about
php artisan route:list --name=courses
php artisan migrate:status
```

3. Pastikan `/courses` masih jalan dan CRUD (tambah/edit/hapus) tidak error.
4. Titik aman Git (**sebelum** mengekstrak zip). Perhatikan tanda `-m`:

```bash
git status
git add .
git commit -m "chore: checkpoint before course search pagination"
```

5. Ekstrak `Pertemuan-13-KursusKu.zip` ke `C:\laragon\www\` dan pilih **Replace**.

**Jangan lanjut** jika Course index sudah error sebelum fitur baru, supaya error lama tidak tercampur.

---

## Langkah 1 — Siapkan data dummy (minimal 21 course)

```bash
php artisan db:seed --class=CoursePaginationSeeder
```

Seeder membuat 24 course dengan kode `DUM-01` sampai `DUM-24` (15 open, 5 closed, 4 draft). Aman dijalankan berulang karena memakai `firstOrCreate` berdasarkan kode.

Isi yang disengaja untuk pengujian:

| Keyword / filter | Hasil yang diharapkan |
| --- | --- |
| `zenith` | tepat 1 course (Pencarian Unik Zenith) |
| `Web Modern` | 5 course |
| `status=open` | lebih dari 10 (jadi ada halaman 2) |
| `tidak-ada-xyz-999` | 0 hasil |

Cek di Tinker bila perlu: `App\Models\Course::count()`.

Membersihkan dummy setelah selesai (aman, enrollment dummy ikut terhapus oleh cascade):

```php
App\Models\Course::where('code', 'like', 'DUM-%')->delete();
```

---

## Langkah 2 — Search `q` di CourseController

`app/Http/Controllers/CourseController.php`, method `index(Request $request)`:

```php
$search = $this->queryString($request, 'q');   // ambil ?q=..., trim
...
->when($search !== '', function ($query) use ($search) {
    $query->where(function ($q) use ($search) {
        $q->where('code', 'like', "%{$search}%")
          ->orWhere('name', 'like', "%{$search}%");
    });
})
```

| Potongan | Arti |
| --- | --- |
| `queryString()` | Mengambil parameter URL sebagai string yang sudah di-trim; jika berupa array dianggap kosong. |
| `when(...)` | Search hanya diterapkan jika keyword tidak kosong. |
| `where(...)` dibungkus closure | Tanda kurung SQL: `(code LIKE .. OR name LIKE ..)`, supaya filter lain tidak ikut tertukar oleh `OR`. |

**Uji:** buka `http://127.0.0.1:8000/courses?q=laravel`. Daftar harus berubah sesuai keyword.

---

## Langkah 3 — Filter status `open / closed / draft`

```php
$status = $this->queryString($request, 'status');
$allowedStatuses = ['open', 'closed', 'draft'];
...
->when(
    in_array($status, $allowedStatuses, true),
    fn ($query) => $query->where('status', $status)
)
```

Hanya nilai yang dikenal yang dipakai. Status kosong atau tidak valid (misalnya `active`) **diabaikan**, bukan menghasilkan 0 data.

**Uji lewat URL:**

```
/courses?status=open
/courses?status=closed
/courses?status=draft
/courses?q=web&status=open
/courses?status=active      ← harus menampilkan semua (nilai tidak dikenal diabaikan)
```

---

## Langkah 4 — Form GET di Blade

`resources/views/courses/index.blade.php` kini memakai layout dan memuat form di atas tabel:

- `method="GET"`, `action="{{ route('courses.index') }}"` (tanpa `@csrf`, karena tidak mengubah data).
- Input `name="q"` dengan `value="{{ $search }}"`.
- Select `name="status"` dengan `@selected($status === '...')`.
- Tombol **Cari** dan **Reset** (kembali ke `/courses`).

**Uji:** isi keyword + status, klik **Cari**. URL menjadi `?q=...&status=...` dan nilai input tetap. Klik **Reset** → URL kembali `/courses`.

---

## Langkah 5 — Pagination 10 item

Pada controller: `->orderBy('name')->orderBy('id')->paginate(10)->withQueryString()`.

Di Blade (`courses/_results.blade.php`): `{{ $courses->links() }}`. Tampilan Bootstrap berasal dari `Paginator::useBootstrapFive()` di `AppServiceProvider`.

Tambahan di bawah tabel: "Menampilkan 1–10 dari 39 kursus".

**Urutan uji:**
1. Buka `/courses?status=open` (lebih dari 10 hasil).
2. Klik **2** di pagination.
3. Periksa URL: harus ada `status=open&page=2`.
4. Data halaman 2 tetap berstatus open; maksimal 10 baris per halaman.

---

## Langkah 6 — Empty state

Tabel memakai `@forelse ... @empty`. Jika 0 hasil: baris pesan *"Tidak ada kursus yang sesuai pencarian/filter."* dan keterangan *"0 kursus ditemukan"*.

**Uji:** `/courses?q=tidak-ada-xyz-999`. Halaman tetap normal, bukan error. 0 hasil adalah kondisi biasa.

---

## Langkah 7 — Uji tanpa JavaScript

1. Tekan **F12** (DevTools) → **Ctrl+Shift+P** → ketik `Disable JavaScript` → Enter.
2. Reload `/courses`. Halaman harus tetap terbuka.
3. Isi q/status, klik **Cari** → hasil berubah lewat reload biasa.
4. Pindah ke halaman 2 → tetap berfungsi.
5. Aktifkan kembali JavaScript (Ctrl+Shift+P → `Enable JavaScript`), reload, pastikan tidak ada masalah.

Evidence: screenshot hasil pencarian saat JavaScript mati beserta URL-nya (`evidence/week-13/04-non-js.png`).

---

## Langkah 8 — (Opsional) Partial view hasil

Bagian hasil (tabel + pagination) dipisah ke `resources/views/courses/_results.blade.php` dengan pembungkus `<div id="course-results">`. Halaman utama memanggilnya dengan `@include('courses._results')`. Ajax cukup mengganti blok ini, bukan seluruh halaman.

## Langkah 9 — Satu query, dua respons

Di `index()`:

```php
if ($request->boolean('partial')) {
    $courses->appends(['partial' => null]);
    return view('courses._results', compact('courses'));
}
return view('courses.index', compact('courses', 'search', 'status'));
```

**Uji manual:** `/courses?q=web&status=open&partial=1` → hanya blok hasil, tanpa navbar dan layout. URL biasa → halaman penuh.

---

## Langkah 10 — Live search jQuery + debounce 300 ms

Kode ada di akhir `resources/js/app.js` (jQuery sudah dimuat sejak pertemuan 9, sehingga tidak ada JS di Blade):

- `input` pada `#live-search`: tunggu 300 ms setelah berhenti mengetik (`clearTimeout` + `setTimeout`), lalu muat hasil.
- `change` pada `#status-filter`: langsung muat hasil.
- Request lama dibatalkan (`abort`) saat ada request baru.
- Efek `opacity-50` saat memuat; pesan merah jika gagal.
- `history.replaceState` menyamakan URL browser dengan hasil yang tampil.

Tombol **Cari** tetap ada sebagai fallback. Biarkan `npm run dev` menyala; perubahan `app.js` terbaca otomatis.

## Langkah 11 — Ajax pagination

Klik nomor halaman di `#course-results .pagination a` dicegat (`e.preventDefault()`), lalu halaman itu dimuat lewat Ajax. `href` link sudah membawa `q` dan `status`, jadi hasil halaman 2 tetap sesuai keyword/filter.

**Uji dengan DevTools → Network** (filter **Fetch/XHR**):

1. Ketik keyword → setelah jeda ±300 ms muncul request `courses?q=...&partial=1`, status 200.
2. Ketik cepat beberapa huruf → hanya satu request yang selesai (debounce).
3. Ganti status → request baru.
4. Klik halaman 2 → request `courses?...page=2&partial=1`, halaman tidak reload penuh.

Evidence: `05-network-ajax.png`.

> Jika Ajax terlalu sulit, berhenti pada versi GET yang sudah benar. Ajax adalah pengayaan.

---

## Langkah 12 — Uji 5 skenario utama

Catat hasil **faktual** di `evidence/week-13/test-matrix.md` (URL, jumlah data, Actual, Pass/Fail).

| No | Skenario | Langkah | Expected |
| --- | --- | --- | --- |
| 1 | 0 hasil | `?q=tidak-ada-xyz-999` | Empty state tampil, tanpa error |
| 2 | 1 hasil | `?q=zenith` | Tepat 1 course |
| 3 | Lebih dari 10 | `?status=open` | Halaman 1 maksimal 10 baris + pagination |
| 4 | Keyword spasi | `?q=Web Modern` | Keyword terbaca benar (5 course dummy) |
| 5 | Filter + page 2 | `?status=open` lalu halaman 2 | q/status tetap |

Tambahan jika Ajax dikerjakan: debounce, filter, pagination Ajax, dan fallback saat JavaScript mati.

**Tes otomatis:**

```bash
php artisan test --filter=CourseSearchTest
php artisan test
```

`CourseSearchTest` mencakup 5 skenario di atas, pencarian kode, filter tiap status, status tidak valid, gabungan q+status, urutan nama, `q[]=x` tanpa error, form GET, halaman penuh vs partial, dan link pagination partial. `HomeDashboardTest` mengecek ringkasan di beranda. `ExampleTest` diperbarui memakai `RefreshDatabase` karena beranda kini membaca database.

> Tes otomatis memakai SQLite in-memory dan **tidak** menggantikan uji manual di browser (terutama non-JS dan Network).

---

## Langkah 13 — Dashboard count (tambahan proyek)

Buka beranda (`/`): kartu **Total Course**, **Course Open**, **Participant**, **Enrollment**. Dua kartu bisa diklik (Course Open mengarah ke `/courses?status=open`, Enrollment ke laporan). Angka dihitung dari database lewat `HomeController`.

Screenshot → `07-dashboard-count.png`.

---

## Langkah 14 — Draft artikel ilmiah

Gunakan kerangka `evidence/week-13/draft-artikel.md`. Prinsipnya: **mulai dari evidence proyek, bukan dari klaim.**

Urutan pengerjaan:
1. Judul sementara (pola: fokus fitur + pendekatan/teknologi + konteks).
2. Problem statement: apa yang sulit saat data Course makin banyak?
3. Gap yang spesifik dan masuk akal (tidak harus penemuan besar).
4. Metode: tulis hanya yang benar-benar dikerjakan.
5. Tabel skenario uji: kolom *Actual* dan *Status* diisi **setelah** uji nyata.
6. Hasil awal: hanya yang sudah terukur.
7. Pembahasan awal dan referensi yang benar-benar dipakai (verifikasi penulis, tahun, judul, DOI/URL).

**Jangan** mengarang nilai performa, jumlah pengguna, persentase keberhasilan, atau status publikasi. Yang belum diukur ditulis sebagai rencana/keterbatasan.

---

## Langkah 15 — Troubleshooting

Urutan: lihat URL → route → controller/query → Blade → baru JavaScript/Ajax.

| Gejala | Kemungkinan penyebab | Periksa |
| --- | --- | --- |
| Search tidak berubah | name input bukan `q` | URL harus `?q=...` |
| Page 2 kehilangan keyword | `withQueryString()` hilang | Controller setelah `paginate(10)` |
| Filter kosong menghasilkan 0 data | `where(status)` tanpa `when` | Gunakan `when` + `allowedStatuses` |
| Pagination tampil berantakan / panah besar | Paginator belum Bootstrap | `Paginator::useBootstrapFive()` di `AppServiceProvider` |
| 0 hasil → error Blade | tidak memakai `@forelse` | `_results.blade.php` |
| Ajax mengirim terlalu banyak request | debounce tidak bekerja | `clearTimeout` + `setTimeout 300` |
| Ajax menampilkan halaman penuh bertumpuk | `partial=1` tidak ditangani | Network + controller |
| Klik halaman me-reload penuh | handler belum aktif / Vite mati | `npm run dev`, Console browser |
| Tanpa JS fitur mati | form GET terhapus | Kembalikan baseline GET |
| `Nothing to seed` / data tidak bertambah | seeder sudah pernah jalan | normal (idempotent); cek `Course::count()` |

Perintah bantu: `php artisan route:list --name=courses`, `php artisan optimize:clear`, `php artisan tinker`.

---

## Langkah 16 — Evidence, commit, dan checklist

```
evidence/week-13/
  01-course-index-normal.png     02-nol-hasil.png
  03-page2-filter.png            04-non-js.png
  05-network-ajax.png (opsional) 06-test-result.png
  07-dashboard-count.png
  test-matrix.md   draft-artikel.md   ai-usage-log.md
```

Yang dikumpulkan: `CourseController@index`, view pagination (`index` + `_results`), test matrix, screenshot, Network evidence jika Ajax dikerjakan.

Commit setelah semuanya benar:

```bash
git add .
git commit -m "feat: add course search, status filter, and pagination"
```

Checklist:

- [ ] Search `q` bekerja (code dan name)
- [ ] Filter open/closed/draft bekerja
- [ ] Pagination 10 item/page
- [ ] `withQueryString()` aktif (q/status tetap saat pindah halaman)
- [ ] Empty state 0 hasil
- [ ] Tanpa JavaScript tetap bekerja
- [ ] 5 skenario dicatat dengan hasil nyata
- [ ] Screenshot/evidence tersimpan
- [ ] Judul sementara, problem statement, gap, metode + tabel uji
- [ ] Hasil awal tidak direkayasa
- [ ] (Opsional) partial view, live search, debounce 300 ms, Ajax pagination, Network evidence

## Pertanyaan cepat (code defense)

1. Jika JavaScript dimatikan, apakah pencarian masih bekerja? Mengapa?
2. Apa akibat menghapus `withQueryString()`?
3. Mengapa filter status tidak boleh diterapkan saat nilainya kosong?
4. Data apa yang benar-benar Anda ukur untuk artikel?

---

## Daftar file di zip ini

| File | Status |
| --- | --- |
| `app/Http/Controllers/CourseController.php` | diubah (`index` dengan search/filter/pagination/partial) |
| `app/Http/Controllers/HomeController.php` | diubah (ringkasan data) |
| `app/Providers/AppServiceProvider.php` | diubah (pagination Bootstrap 5) |
| `resources/views/courses/index.blade.php` | diganti (form GET + `@include`) |
| `resources/views/courses/_results.blade.php` | baru (tabel + pagination) |
| `resources/views/home.blade.php` | diubah (kartu ringkasan) |
| `resources/js/app.js` | diubah (live search ditambahkan di akhir; kode pertemuan 9 tetap) |
| `database/seeders/CoursePaginationSeeder.php` | baru (24 data dummy) |
| `tests/Feature/CourseSearchTest.php` | baru |
| `tests/Feature/HomeDashboardTest.php` | baru |
| `tests/Feature/ExampleTest.php` | diubah (`RefreshDatabase`) |
| `evidence/week-13/*` | baru (test matrix, draft artikel, log AI) |
| `README.md`, `LANGKAH-PEMBUATAN.md` | diperbarui |

Setelah ekstrak: `php artisan db:seed --class=CoursePaginationSeeder`, pastikan `npm run dev` menyala, lalu buka `/courses`.
