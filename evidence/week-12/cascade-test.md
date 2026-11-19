# Catatan Cascade Test — Week 12

Gunakan data dummy saja. Jalankan di `php artisan tinker`. Status course dummy memakai `open` (sesuai status di proyek ini).

## A. Hapus Course dummy

```php
use App\Models\Course;
use App\Models\Participant;
use App\Models\Enrollment;

$course = Course::create([
    'code' => 'TEST-CASCADE-COURSE',
    'name' => 'Course Cascade Test',
    'fee' => 10000,
    'quota' => 5,
    'start_date' => now()->addWeek()->toDateString(),
    'status' => 'open',
]);

$participant = Participant::create([
    'name' => 'Dummy Cascade Course',
    'email' => 'cascade.course@example.test',
]);

Enrollment::create([
    'participant_id' => $participant->id,
    'course_id' => $course->id,
    'status' => 'registered',
]);

Enrollment::where('course_id', $course->id)->count();   // harapan: 1
$course->delete();
Enrollment::where('course_id', $course->id)->count();   // harapan: 0
Participant::find($participant->id) !== null;            // harapan: true
```

| Hasil | Harapan | Aktual |
| --- | --- | --- |
| count sebelum delete | 1 | |
| count sesudah delete | 0 | |
| participant masih ada | true | |

## B. Hapus Participant dummy

```php
$course2 = Course::create([
    'code' => 'TEST-CASCADE-PART',
    'name' => 'Participant Cascade Test',
    'fee' => 10000,
    'quota' => 5,
    'start_date' => now()->addDays(10)->toDateString(),
    'status' => 'open',
]);

$participant2 = Participant::create([
    'name' => 'Dummy Participant Delete',
    'email' => 'cascade.participant@example.test',
]);

Enrollment::create([
    'participant_id' => $participant2->id,
    'course_id' => $course2->id,
    'status' => 'registered',
]);

Enrollment::where('participant_id', $participant2->id)->count();  // harapan: 1
$participant2->delete();
Enrollment::where('participant_id', $participant2->id)->count();  // harapan: 0
Course::find($course2->id) !== null;                               // harapan: true
```

| Hasil | Harapan | Aktual |
| --- | --- | --- |
| count sebelum delete | 1 | |
| count sesudah delete | 0 | |
| course masih ada | true | |

## C. Bersihkan sisa dummy

```php
$course2->delete();
$participant->delete();
```

## D. Penjelasan dengan bahasa sendiri

Apa yang ikut terhapus dan apa yang tetap ada? Mengapa cascade dipasang pada foreign key `enrollments`, bukan antara Course dan Participant?

........
