<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;

/**
 * Participant dummy (Langkah 5). Aman dijalankan berulang: memakai firstOrCreate
 * berdasarkan email, jadi tidak membuat data ganda.
 *
 * Jalankan: php artisan db:seed --class=ParticipantSeeder
 */
class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $participants = [
            ['alya@example.test', 'Alya Rahma', '081200000001'],
            ['bima@example.test', 'Bima Putra', '081200000002'],
            ['citra@example.test', 'Citra Nabila', null],
        ];

        foreach ($participants as [$email, $name, $phone]) {
            Participant::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'phone' => $phone],
            );
        }
    }
}
