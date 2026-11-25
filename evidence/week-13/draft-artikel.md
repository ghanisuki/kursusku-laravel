# Draft Artikel Ilmiah — KursusKu

> Draft awal. Seluruh isi berasal dari pengujian yang benar-benar dilakukan pada proyek KursusKu.

## 1. Judul sementara

Pengembangan Fitur Pencarian, Filter Status, dan Pagination pada Sistem Pendaftaran Kursus Berbasis Laravel

## 2. Problem statement

Seiring bertambahnya data kursus, menampilkan seluruh data dalam satu halaman membuat daftar menjadi panjang dan menyulitkan pengguna yang hanya ingin menemukan satu kursus tertentu. Pada pengujian ini, database berisi 31 kursus, dan tanpa pencarian maupun pembagian halaman pengguna harus menggulir daftar yang panjang untuk menemukan data yang dicari. Selain itu, fitur pencarian perlu tetap berfungsi dengan benar pada kondisi tertentu, seperti kata kunci yang tidak cocok dengan data apa pun, kata kunci yang mengandung spasi, dan perpindahan halaman saat filter sedang aktif.

## 3. Gap

Pada versi sebelumnya, sistem KursusKu hanya menampilkan daftar kursus tanpa pencarian, filter, dan pembagian halaman. Penelitian ini menambahkan ketiganya dengan pendekatan server-side, sehingga fitur tetap berjalan tanpa JavaScript, lalu menambahkan Ajax sebagai peningkatan kenyamanan, bukan sebagai syarat agar fitur berfungsi.

Catatan: kajian pustaka belum ditulis. Referensi harus dicari dan ditambahkan sendiri dari sumber yang nyata.

## 4. Metode

Sistem dibangun dengan framework Laravel, basis data MySQL, dan tampilan Bootstrap 5. Fitur yang dikembangkan:

- Pencarian kursus berdasarkan kode atau nama melalui parameter `q`.
- Filter berdasarkan status (open, closed, draft) melalui parameter `status`.
- Pagination 10 data per halaman dengan `withQueryString()`, agar parameter pencarian dan filter terbawa saat berpindah halaman.
- Pesan khusus saat hasil pencarian kosong.
- Form berbasis metode GET, sehingga berfungsi tanpa JavaScript.
- Pencarian Ajax dengan jeda sekitar 300 ms sebagai pengayaan.

Pengujian dilakukan dengan data uji berupa 31 kursus (7 data lama dan 24 data dummy), 19 di antaranya berstatus Open. Pengujian terdiri dari tes otomatis Laravel dan uji manual lima skenario di browser.

## 5. Tabel uji

| No | Skenario | Alamat | Hasil | Status |
| --- | --- | --- | --- | --- |
| 1 | Hasil kosong | `/courses?q=tidak-ada-xyz-999` | Pesan kosong tampil, 0 kursus ditemukan, tanpa error | Pass |
| 2 | Satu hasil | `/courses?q=zenith` | 1 kursus tampil | Pass |
| 3 | Lebih dari 10 hasil | `/courses?status=open` | 10 baris dari 19 kursus, pagination muncul | Pass |
| 4 | Kata kunci berspasi | `/courses?q=Web Modern` | 5 kursus Web Modern tampil | Pass |
| 5 | Filter dan halaman 2 | `/courses?status=open`, halaman 2 | `status=open` tetap di alamat, data tetap berstatus Open | Pass |
| 6 | JavaScript dimatikan | `/courses?q=web&status=open` | Pencarian dan filter tetap berfungsi, 4 baris Open tampil | Pass |
| 7 | Ajax | Mengetik `web` di kolom cari | Request xhr dari app.js berstatus 200, tabel berubah tanpa reload | Pass |
| 8 | Tes otomatis | `php artisan test` | 59 tes lulus | Pass |

## 6. Hasil awal

Seluruh skenario yang diuji berjalan sesuai harapan. Pencarian dan filter berfungsi melalui form GET tanpa JavaScript, dan parameter tetap terbawa saat berpindah halaman. Pada uji Ajax, ketika pengguna mengetik cepat, request lama dibatalkan dan hanya request terakhir yang dipakai untuk memperbarui tabel. Sebanyak 59 tes otomatis lulus.

Keterbatasan: pengujian baru dilakukan pada data 31 kursus di lingkungan lokal. Skenario tambahan (filter closed, filter draft, status tidak valid, pencarian kode, dan tombol Reset) belum diuji manual sehingga belum dilaporkan di sini. Kinerja pada data yang jauh lebih besar belum diukur.

## 7. Referensi

[ISI: tambahkan referensi dari sumber nyata, misalnya dokumentasi Laravel tentang pagination]