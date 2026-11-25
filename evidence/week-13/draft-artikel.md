# Draft Artikel Ilmiah — KursusKu

> Draft awal, belum harus sempurna. Aturan: tulis hanya yang benar-benar dikerjakan dan diuji. Bagian bertanda `[ISI]` adalah milik Anda. Jangan mengarang angka performa, jumlah pengguna, persentase keberhasilan, atau status publikasi.

## 1. Judul sementara

Pola: [fokus sistem/fitur] + [pendekatan/teknologi] + [konteks].

Contoh arah (ubah dengan kata-kata sendiri):
- Pengembangan Sistem Pendaftaran Kursus Berbasis Laravel dengan Pencarian dan Pagination Server-Side
- [ISI judul pilihan Anda]

## 2. Problem statement

Jelaskan masalah yang benar-benar terjadi. Pertanyaan pemandu:
- Apa yang sulit ketika data Course semakin banyak (daftar panjang dalam satu halaman)?
- Apa yang dialami pengguna saat mencari satu kursus tertentu?
- Mengapa memuat seluruh data dalam satu halaman bermasalah?

[ISI 1–2 paragraf]

## 3. Gap sederhana

Bandingkan kondisi awal dengan kebutuhan yang belum terpenuhi. Gap tidak harus penemuan besar, tetapi harus spesifik dan masuk akal.

- Kondisi awal (fakta dari proyek): sebelum pertemuan 13, halaman daftar Course menampilkan seluruh data tanpa pencarian, filter, maupun pagination.
- Kebutuhan yang belum terpenuhi: [ISI]

## 4. Tujuan

[ISI satu kalimat tujuan, misalnya mengembangkan dan menguji fitur pencarian, filter status, dan pagination pada daftar Course]

## 5. Metode

Tulis secara faktual. Daftar di bawah sesuai yang dikerjakan di proyek; hapus yang tidak Anda kerjakan.

1. Identifikasi kebutuhan Course index.
2. Perancangan data (tabel `courses`, `participants`, `enrollments`).
3. Implementasi Laravel: search `q` pada kolom `code` dan `name`, filter status (`open`, `closed`, `draft`), pagination 10 item per halaman dengan `withQueryString()`.
4. Fallback tanpa JavaScript menggunakan form HTTP GET.
5. Progressive enhancement Ajax (partial view, debounce 300 ms, Ajax pagination) — *tulis hanya jika dikerjakan*.
6. Pengujian fungsional skenario (manual di browser dan tes otomatis Laravel).

Lingkungan: [ISI versi PHP, Laravel, MySQL, browser yang benar-benar dipakai]

## 6. Skenario uji

Salin hasil dari `test-matrix.md`. Kolom Actual dan Status diisi **setelah** uji nyata.

| Skenario | Input | Expected | Actual | Status |
| --- | --- | --- | --- | --- |
| 0 hasil | `q=tidak-ada-xyz-999` | Empty state | | |
| 1 hasil | `q=zenith` | 1 record | | |
| >10 hasil | `status=open` | Pagination | | |
| Keyword spasi | `q=Web Modern` | Keyword terbaca | | |
| Filter + page 2 | `status=open&page=2` | Query state tetap | | |

## 7. Hasil awal

Laporkan hanya hasil yang sudah diukur, misalnya jumlah skenario lulus, perilaku pagination, query string yang tetap, hasil request Ajax jika benar-benar diuji.

[ISI setelah pengujian. Contoh format: "Dari 5 skenario utama, ... lulus dan ... gagal. Skenario ... gagal karena ..."]

Jumlah data saat pengujian: [ISI]

## 8. Pembahasan awal

Jelaskan mengapa hasil itu penting, tanpa melebih-lebihkan. Ide yang boleh dibahas bila sesuai bukti Anda:
- Fallback GET menjaga fungsi pencarian tanpa JavaScript.
- Pagination membuat daftar panjang lebih mudah ditelusuri.
- Debounce mengurangi request saat mengetik cepat (hanya jika Ajax benar-benar diuji).

[ISI]

## 9. Keterbatasan

Tulis yang belum diukur sebagai keterbatasan atau rencana, misalnya: belum ada pengukuran waktu respons, data uji berupa data dummy, belum diuji pada banyak pengguna bersamaan.

[ISI]

## 10. Referensi awal

Gunakan hanya sumber yang benar-benar dipakai. Verifikasi penulis, tahun, judul, dan DOI/URL sebelum dimasukkan.

Sumber teknis (isi tanggal akses dan versi yang Anda pakai):
- Laravel Documentation — Pagination, Requests, Eloquent/Query Builder, Views. https://laravel.com/docs (akses: [ISI])
- jQuery API Documentation — `jQuery.get()` dan event handling. https://api.jquery.com (akses: [ISI])

Sumber penelitian untuk konteks dan gap (cari sendiri, jangan dikarang):
- [ISI] — saran kata kunci pencarian: "server-side pagination web application", "search usability data table", "progressive enhancement web", "Laravel pagination". Cari di Google Scholar atau portal jurnal kampus, lalu catat penulis, tahun, judul, dan DOI.

## Checklist integritas akademik

- [ ] Semua angka di artikel berasal dari pengujian saya sendiri
- [ ] Kolom Actual/Status diisi setelah uji nyata
- [ ] Bagian yang belum diuji ditulis sebagai rencana/keterbatasan
- [ ] Semua referensi sudah saya cek keberadaannya
- [ ] Penggunaan AI dicatat di `ai-usage-log.md`
