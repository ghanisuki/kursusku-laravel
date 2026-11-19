<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Participant;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pertemuan 12: relasi Course - Participant - Enrollment.
 * Jalankan: php artisan test --filter=EnrollmentTest
 */
class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // ---------- Relasi Eloquent ----------

    public function test_relasi_eloquent_terhubung_dua_arah(): void
    {
        $participant = Participant::factory()->create();
        $course = Course::factory()->create();
        $enrollment = Enrollment::create([
            'participant_id' => $participant->id,
            'course_id' => $course->id,
            'status' => 'registered',
        ]);

        $this->assertTrue($enrollment->participant->is($participant));
        $this->assertTrue($enrollment->course->is($course));
        $this->assertTrue($participant->enrollments->first()->is($enrollment));
        $this->assertTrue($course->enrollments->first()->is($enrollment));
        $this->assertTrue($participant->courses->first()->is($course));
        $this->assertTrue($course->participants->first()->is($participant));
        $this->assertSame('registered', $course->participants->first()->pivot->status);
    }

    // ---------- Test matrix langkah 10 ----------

    public function test_01_form_menampilkan_participant_dan_course_open(): void
    {
        Participant::factory()->create(['name' => 'Alya Rahma']);
        Course::factory()->create(['name' => 'Laravel Dasar', 'status' => 'open']);
        Course::factory()->create(['name' => 'Kursus Draft Rahasia', 'status' => 'draft']);

        $this->get(route('enrollments.create'))
            ->assertOk()
            ->assertSee('Alya Rahma')
            ->assertSee('Laravel Dasar')
            ->assertDontSee('Kursus Draft Rahasia');
    }

    public function test_02_participant_kosong_ditolak(): void
    {
        $course = Course::factory()->create();

        $this->post(route('enrollments.store'), ['participant_id' => '', 'course_id' => $course->id])
            ->assertSessionHasErrors('participant_id');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_03_course_kosong_ditolak(): void
    {
        $participant = Participant::factory()->create();

        $this->post(route('enrollments.store'), ['participant_id' => $participant->id, 'course_id' => ''])
            ->assertSessionHasErrors('course_id');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_04_enrollment_tersimpan_dengan_status_registered(): void
    {
        $alya = Participant::factory()->create();
        $laravel = Course::factory()->create();

        $this->post(route('enrollments.store'), [
            'participant_id' => $alya->id,
            'course_id' => $laravel->id,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('enrollments.index'))
            ->assertSessionHas('success', 'Enrollment berhasil disimpan.');

        $this->assertDatabaseHas('enrollments', [
            'participant_id' => $alya->id,
            'course_id' => $laravel->id,
            'status' => 'registered',
        ]);
    }

    public function test_05_enrollment_ganda_ditolak(): void
    {
        $alya = Participant::factory()->create();
        $laravel = Course::factory()->create();
        Enrollment::create(['participant_id' => $alya->id, 'course_id' => $laravel->id]);

        $this->post(route('enrollments.store'), [
            'participant_id' => $alya->id,
            'course_id' => $laravel->id,
        ])->assertSessionHasErrors([
            'course_id' => 'Participant ini sudah terdaftar pada course tersebut.',
        ]);

        $this->assertDatabaseCount('enrollments', 1);
    }

    public function test_06_participant_sama_boleh_ikut_course_lain(): void
    {
        $alya = Participant::factory()->create();
        $laravel = Course::factory()->create();
        $php = Course::factory()->create();
        Enrollment::create(['participant_id' => $alya->id, 'course_id' => $laravel->id]);

        $this->post(route('enrollments.store'), [
            'participant_id' => $alya->id,
            'course_id' => $php->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame(2, Enrollment::where('participant_id', $alya->id)->count());
    }

    public function test_07_participant_lain_boleh_ikut_course_yang_sama(): void
    {
        $alya = Participant::factory()->create();
        $bima = Participant::factory()->create();
        $laravel = Course::factory()->create();
        Enrollment::create(['participant_id' => $alya->id, 'course_id' => $laravel->id]);

        $this->post(route('enrollments.store'), [
            'participant_id' => $bima->id,
            'course_id' => $laravel->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame(2, Enrollment::where('course_id', $laravel->id)->count());
    }

    public function test_08_laporan_menampilkan_data_lintas_tabel(): void
    {
        $alya = Participant::factory()->create(['name' => 'Alya Rahma']);
        $course = Course::factory()->create([
            'name' => 'Laravel Dasar',
            'start_date' => '2026-12-01',
        ]);
        Enrollment::create(['participant_id' => $alya->id, 'course_id' => $course->id]);

        $this->get(route('enrollments.index'))
            ->assertOk()
            ->assertSee('Alya Rahma')
            ->assertSee('Laravel Dasar')
            ->assertSee('01-12-2026')
            ->assertSee('registered');
    }

    public function test_laporan_kosong_menampilkan_pesan(): void
    {
        $this->get(route('enrollments.index'))
            ->assertOk()
            ->assertSee('Belum ada enrollment.');
    }

    // ---------- Validasi tambahan ----------

    public function test_id_yang_tidak_ada_ditolak_oleh_exists(): void
    {
        $this->post(route('enrollments.store'), ['participant_id' => 999999, 'course_id' => 999999])
            ->assertSessionHasErrors(['participant_id', 'course_id']);
    }

    public function test_unique_index_database_menolak_pasangan_ganda(): void
    {
        $alya = Participant::factory()->create();
        $course = Course::factory()->create();
        Enrollment::create(['participant_id' => $alya->id, 'course_id' => $course->id]);

        $this->expectException(QueryException::class);

        // Melewati validasi sama sekali: proteksi lapis kedua ada di database.
        Enrollment::create(['participant_id' => $alya->id, 'course_id' => $course->id]);
    }

    public function test_email_participant_unik_di_database(): void
    {
        Participant::create(['name' => 'A', 'email' => 'sama@example.test']);

        $this->expectException(QueryException::class);

        Participant::create(['name' => 'B', 'email' => 'sama@example.test']);
    }

    // ---------- Cascade ----------

    public function test_cascade_hapus_course_menghapus_enrollment_tapi_participant_tetap(): void
    {
        $participant = Participant::factory()->create();
        $course = Course::factory()->create(['code' => 'TEST-CASCADE-COURSE']);
        Enrollment::create(['participant_id' => $participant->id, 'course_id' => $course->id]);

        $this->assertSame(1, Enrollment::where('course_id', $course->id)->count());

        $course->delete();

        $this->assertSame(0, Enrollment::where('course_id', $course->id)->count());
        $this->assertNotNull(Participant::find($participant->id));
    }

    public function test_cascade_hapus_participant_menghapus_enrollment_tapi_course_tetap(): void
    {
        $participant = Participant::factory()->create();
        $course = Course::factory()->create(['code' => 'TEST-CASCADE-PART']);
        Enrollment::create(['participant_id' => $participant->id, 'course_id' => $course->id]);

        $this->assertSame(1, Enrollment::where('participant_id', $participant->id)->count());

        $participant->delete();

        $this->assertSame(0, Enrollment::where('participant_id', $participant->id)->count());
        $this->assertNotNull(Course::find($course->id));
    }

    public function test_hapus_course_lewat_ui_ikut_menghapus_enrollment(): void
    {
        $participant = Participant::factory()->create();
        $course = Course::factory()->create();
        Enrollment::create(['participant_id' => $participant->id, 'course_id' => $course->id]);

        $this->delete(route('courses.destroy', $course))->assertRedirect(route('courses.index'));

        $this->assertDatabaseCount('enrollments', 0);
        $this->assertDatabaseHas('participants', ['id' => $participant->id]);
    }

    // ---------- Ajax availability & halaman detail course ----------

    public function test_availability_dihitung_dari_database(): void
    {
        $course = Course::factory()->create(['name' => 'Laravel Dasar', 'quota' => 5]);
        Enrollment::factory()->count(2)->create(['course_id' => $course->id]);

        $this->getJson(route('courses.availability', $course))
            ->assertOk()
            ->assertJson(['course' => 'Laravel Dasar', 'remaining' => 3]);
    }

    public function test_availability_course_tidak_ada_404(): void
    {
        $this->getJson('/courses/999999/availability')->assertNotFound();
    }

    public function test_detail_course_menampilkan_peserta_terdaftar(): void
    {
        $participant = Participant::factory()->create(['name' => 'Bima Putra']);
        $course = Course::factory()->create();
        Enrollment::create(['participant_id' => $participant->id, 'course_id' => $course->id]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Bima Putra');
    }

    public function test_url_lama_pendaftaran_diarahkan_ke_form_enrollment(): void
    {
        $this->get('/pendaftaran')->assertRedirect('/enrollments/create');
    }
}
