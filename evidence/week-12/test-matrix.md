# Test Matrix Week 12 — Relasi Course, Participant, Enrollment

Centang / isi setelah benar-benar dicoba di browser dan database. Kolom Expected adalah hasil yang diharapkan, bukan bukti.

## A. End-to-end (Langkah 10)

| No | Skenario | Expected | Actual | Status | Bukti |
| --- | --- | --- | --- | --- | --- |
| 1 | Buka `/enrollments/create` | Participant dan course (status open) tampil di select | | Pass / Fail | |
| 2 | Submit participant kosong | Ditolak, error dekat field | | Pass / Fail | |
| 3 | Submit course kosong | Ditolak, error dekat field | | Pass / Fail | |
| 4 | Alya → Laravel Dasar | Tersimpan, status `registered` | | Pass / Fail | |
| 5 | Alya → Laravel Dasar lagi | Ditolak: "Participant ini sudah terdaftar pada course tersebut." | | Pass / Fail | |
| 6 | Alya → PHP Dasar | Tersimpan | | Pass / Fail | |
| 7 | Bima → Laravel Dasar | Tersimpan | | Pass / Fail | |
| 8 | Buka `/enrollments` | Nama participant, course, start date, status benar | | Pass / Fail | |

## B. Cascade (Langkah 11-12)

| No | Skenario | Expected | Actual | Status | Bukti |
| --- | --- | --- | --- | --- | --- |
| 9 | Hapus Course dummy | Enrollment-nya hilang (count 0); Participant tetap ada | | Pass / Fail | |
| 10 | Hapus Participant dummy | Enrollment-nya hilang (count 0); Course tetap ada | | Pass / Fail | |

## C. Pemeriksaan database

```sql
SELECT id, participant_id, course_id, status FROM enrollments ORDER BY id DESC;
SHOW CREATE TABLE enrollments;
```

Tempel hasil / catatan (FK, `ON DELETE CASCADE`, unique index gabungan):

```
(tempel di sini)
```

## D. Tes otomatis

```bash
php artisan test --filter=EnrollmentTest
```

```
(tempel ringkasan output)
```

## E. Pertanyaan dosen (code defense)

1. Dari mana `participant->name` berasal? ........
2. Dari mana `course->start_date` berasal? ........
3. Mengapa enrollment menyimpan id, bukan nama? ........

## F. Refleksi

1. Apa fungsi foreign key dibanding sekadar menyimpan angka id?
2. Mengapa unique harus pada pasangan participant-course?
3. Mengapa `start_date` tidak disimpan ulang pada `enrollments`?
4. Apa yang terjadi jika cascade tidak dipasang?
5. Apa perbedaan `belongsTo()` dan `hasMany()`?
