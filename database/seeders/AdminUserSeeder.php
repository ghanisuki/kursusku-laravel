<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Password disimpan dalam bentuk hash (bcrypt), bukan teks biasa.
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator KursusKu',
                'email' => 'admin@kursusku.test',
                'password' => Hash::make('Admin#12345'),
                'role' => 'admin',
                'status' => 'active',
                'position' => 'Administrator Sistem',
                'institution' => 'KursusKu',
            ]
        );

        User::updateOrCreate(
            ['username' => 'peserta'],
            [
                'name' => 'Peserta Contoh',
                'email' => 'peserta@kursusku.test',
                'password' => Hash::make('Peserta#12345'),
                'role' => 'user',
                'status' => 'active',
                'position' => 'Peserta',
                'institution' => 'KursusKu',
            ]
        );
    }
}
