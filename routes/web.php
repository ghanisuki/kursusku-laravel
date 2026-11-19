<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// URL lama pertemuan 9. Form dummy diganti form enrollment yang memakai database.
Route::redirect('/pendaftaran', '/enrollments/create');

// Sisa kursi untuk Ajax (pertemuan 9), kini dihitung dari tabel enrollments.
Route::get('/courses/{course}/availability', [CourseController::class, 'availability'])
    ->name('courses.availability');

Route::resource('courses', CourseController::class);

Route::resource('enrollments', EnrollmentController::class)
    ->only(['index', 'create', 'store']);
