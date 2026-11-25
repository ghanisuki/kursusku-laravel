<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

/**
 * 24 course dummy untuk menguji search, filter status, dan pagination (Pertemuan 13).
 * Kode diawali DUM- agar mudah dibersihkan. Aman dijalankan berulang (firstOrCreate by code).
 *
 * Jalankan : php artisan db:seed --class=CoursePaginationSeeder
 * Bersihkan: Course::where('code', 'like', 'DUM-%')->delete();   (di tinker)
 *
 * Komposisi: 15 open, 5 closed, 4 draft.
 * Keyword uji: "zenith" = 1 hasil, "Web Modern" = 5 hasil, status open = lebih dari 10.
 */
class CoursePaginationSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['Web Modern Dasar', 'open'],
            ['Web Modern Lanjutan', 'open'],
            ['Web Modern Responsif', 'closed'],
            ['Web Modern Performa', 'draft'],
            ['Web Modern Aksesibilitas', 'open'],
            ['Laravel Praktis Routing', 'open'],
            ['Laravel Praktis Controller', 'open'],
            ['Laravel Praktis Blade', 'open'],
            ['Laravel Praktis Validasi', 'closed'],
            ['Laravel Praktis Relasi', 'open'],
            ['Basis Data MySQL Query', 'open'],
            ['Basis Data MySQL Join', 'draft'],
            ['Basis Data MySQL Index', 'closed'],
            ['PHP Lanjutan OOP', 'open'],
            ['PHP Lanjutan Namespace', 'open'],
            ['PHP Lanjutan Composer', 'draft'],
            ['JavaScript Praktis DOM', 'open'],
            ['JavaScript Praktis Event', 'open'],
            ['JavaScript Praktis Fetch', 'closed'],
            ['jQuery Praktis Selector', 'open'],
            ['jQuery Praktis Ajax', 'draft'],
            ['Git dan GitHub Dasar', 'open'],
            ['Pengujian Aplikasi Web', 'closed'],
            ['Pencarian Unik Zenith', 'open'],
        ];

        foreach ($courses as $i => [$name, $status]) {
            $number = $i + 1;

            Course::firstOrCreate(
                ['code' => sprintf('DUM-%02d', $number)],
                [
                    'name' => $name,
                    'description' => 'Data dummy pengujian pagination dan pencarian.',
                    'fee' => 250000 + ($number * 10000),
                    'quota' => 15 + ($number % 10),
                    'start_date' => now()->addDays(10 + $number)->toDateString(),
                    'status' => $status,
                ]
            );
        }
    }
}
