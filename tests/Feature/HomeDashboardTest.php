<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Milestone 13: dashboard count di beranda.
 * Jalankan: php artisan test --filter=HomeDashboardTest
 */
class HomeDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_beranda_menampilkan_ringkasan_jumlah_data(): void
    {
        $courses = Course::factory()->count(2)->create(['status' => 'open']);
        Course::factory()->create(['status' => 'draft']);
        $participants = Participant::factory()->count(2)->create();
        Enrollment::create([
            'participant_id' => $participants->first()->id,
            'course_id' => $courses->first()->id,
        ]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('Ringkasan Data');
        $this->assertSame(
            ['courses' => 3, 'open_courses' => 2, 'participants' => 2, 'enrollments' => 1],
            $response->viewData('stats')
        );
    }

    public function test_beranda_aman_saat_database_masih_kosong(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $this->assertSame(
            ['courses' => 0, 'open_courses' => 0, 'participants' => 0, 'enrollments' => 0],
            $response->viewData('stats')
        );
    }
}
