# ERD Final — Week 12

Gambar: [erd-final.svg](erd-final.svg) (buka di browser, lalu screenshot sebagai bukti).

Versi teks (Mermaid, bisa dipratinjau di GitHub / VS Code):

```mermaid
erDiagram
    COURSES ||--o{ ENROLLMENTS : "hasMany"
    PARTICIPANTS ||--o{ ENROLLMENTS : "hasMany"

    COURSES {
        bigint id PK
        string code UK
        string name
        text description
        uint fee
        uint quota
        date start_date
        string status
    }
    PARTICIPANTS {
        bigint id PK
        string name
        string email UK
        string phone
    }
    ENROLLMENTS {
        bigint id PK
        bigint participant_id FK
        bigint course_id FK
        string status
    }
```

Constraint tambahan pada `enrollments`: `UNIQUE (participant_id, course_id)`, kedua FK `ON DELETE CASCADE`.

## Jawaban checkpoint dosen

1. Jika Alya ikut 3 kursus, berapa baris enrollment? → ........
2. Jika 20 peserta ikut Laravel Dasar, berapa baris enrollment untuk course itu? → ........
3. Mengapa `course_name` tidak perlu disimpan lagi di `enrollments`? → ........
