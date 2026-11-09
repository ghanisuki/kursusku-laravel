# Test Matrix Week 11 — Course CRUD Lanjutan

Isi kolom **Actual**, **Status**, dan **Bukti** setelah benar-benar dicoba di browser. Kolom Expected adalah hasil yang diharapkan, bukan bukti. Minimal 12 kasus; kasus 1, 4, 6, 7, 10, 11, 12, 13, 15 sangat disarankan.

| No | Kasus | Input / tindakan | Expected | Actual | Status | Bukti |
| --- | --- | --- | --- | --- | --- | --- |
| 1 | Duplicate code saat create | code Course yang sudah ada | Ditolak; error dekat field code | | Pass / Fail | |
| 2 | Nama kosong | name = "" | Ditolak; name required | | Pass / Fail | |
| 3 | Nama terlalu pendek | name = "AI" | Ditolak; minimal 3 karakter | | Pass / Fail | |
| 4 | Fee negatif | fee = -1000 | Ditolak; minimal 0 | | Pass / Fail | |
| 5 | Fee bukan integer | fee = abc | Ditolak; harus integer | | Pass / Fail | |
| 6 | Quota nol | quota = 0 | Ditolak | | Pass / Fail | |
| 7 | Quota 501 | quota = 501 | Ditolak | | Pass / Fail | |
| 8 | Quota minimum | quota = 1 | Diterima | | Pass / Fail | |
| 9 | Quota maksimum | quota = 500 | Diterima | | Pass / Fail | |
| 10 | Tanggal invalid | nilai bukan tanggal (DevTools / request manual) | Ditolak oleh rule date | | Pass / Fail | |
| 11 | Status invalid | status = active (ubah option via DevTools) | Ditolak | | Pass / Fail | |
| 12 | Update dengan code sendiri | edit tanpa mengubah code | Berhasil | | Pass / Fail | |
| 13 | Update ke code Course lain | ganti code ke code Course lain | Ditolak unique | | Pass / Fail | |
| 14 | Update normal | ubah nama/fee/quota/status valid | Berhasil; detail menampilkan data baru | | Pass / Fail | |
| 15 | Delete | hapus Course dummy | Hilang dari index + flash sukses | | Pass / Fail | |

Hasil tes otomatis: `php artisan test --filter=CourseCrudTest` → tempel ringkasan hasil di sini:

```
(tempel output)
```

## Refleksi

1. Mengapa validasi dipindahkan dari controller ke Form Request?

   ...

2. Mengapa unique rule pada update berbeda dari store?

   ...

3. Apa fungsi `@method('PUT')` dan `@method('DELETE')`?

   ...
