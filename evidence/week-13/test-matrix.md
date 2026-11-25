# Test Matrix Week 13 — Search, Filter Status, Pagination

Isi kolom **Actual**, **Status**, dan **Bukti** setelah benar-benar menguji di browser. Kolom Expected adalah hasil yang diharapkan, bukan bukti. Jangan menulis Pass sebelum diuji.

Alamat dasar: `http://127.0.0.1:8000`

## A. 5 skenario utama

| No | Skenario | URL / langkah | Expected | Actual | Status | Bukti |
| --- | --- | --- | --- | --- | --- | --- |
| 1 | 0 hasil | `/courses?q=tidak-ada-xyz-999` | Empty state tampil, tanpa error | | Pass / Fail | |
| 2 | 1 hasil | `/courses?q=zenith` | Tepat 1 course | | Pass / Fail | |
| 3 | Lebih dari 10 hasil | `/courses?status=open` | Halaman 1 maksimal 10 baris + pagination | | Pass / Fail | |
| 4 | Keyword dengan spasi | `/courses?q=Web Modern` | Keyword terbaca benar (5 course dummy) | | Pass / Fail | |
| 5 | Filter + page 2 | `/courses?status=open`, klik halaman 2 | q/status tetap, data tetap berstatus open | | Pass / Fail | |

Jumlah total course di database saat uji: ........ (isi dari `App\Models\Course::count()`)

## B. Pengujian tambahan

| No | Skenario | Langkah | Expected | Actual | Status | Bukti |
| --- | --- | --- | --- | --- | --- | --- |
| 6 | Filter closed | `?status=closed` | Hanya status closed | | Pass / Fail | |
| 7 | Filter draft | `?status=draft` | Hanya status draft | | Pass / Fail | |
| 8 | Status tidak valid | `?status=active` | Semua data tampil (diabaikan) | | Pass / Fail | |
| 9 | Cari kode | `?q=DUM-0` | Course berkode DUM-0x tampil | | Pass / Fail | |
| 10 | Search + status | `?q=Web Modern&status=open` | Hanya Web Modern berstatus open | | Pass / Fail | |
| 11 | Tombol Reset | klik Reset | URL kembali `/courses`, semua data | | Pass / Fail | |
| 12 | **JavaScript dimatikan** | F12 → Disable JavaScript, cari + pindah halaman | Form GET dan pagination tetap bekerja | | Pass / Fail | |
| 13 | Partial | `?q=zenith&partial=1` | Hanya blok hasil, tanpa navbar | | Pass / Fail | |
| 14 | Dashboard count | buka `/` | Angka sesuai database | | Pass / Fail | |

## C. Jika Ajax dikerjakan (opsional)

| No | Skenario | Expected | Actual | Status | Bukti |
| --- | --- | --- | --- | --- | --- |
| 15 | Debounce | Mengetik cepat: hanya 1 request setelah jeda ±300 ms | | Pass / Fail | |
| 16 | Filter Ajax | Ganti status memperbarui hasil tanpa reload | | Pass / Fail | |
| 17 | Pagination Ajax | Klik halaman 2 tanpa reload penuh | | Pass / Fail | |
| 18 | Network evidence | Request `partial=1` status 200 terlihat di tab Network | | Pass / Fail | |

## D. Tes otomatis

```bash
php artisan test --filter=CourseSearchTest
php artisan test
```

```
(tempel ringkasan output)
```

## E. Pertanyaan cepat

1. Jika JavaScript dimatikan, apakah pencarian masih bekerja? Mengapa? ........
2. Apa akibat menghapus `withQueryString()`? ........
3. Mengapa filter status tidak boleh diterapkan saat nilainya kosong? ........
4. Data apa yang benar-benar Anda ukur untuk artikel? ........
