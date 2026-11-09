<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test matrix Pertemuan 11 (15 kasus) dalam bentuk tes otomatis.
 * Jalankan: php artisan test --filter=CourseCrudTest
 */
class CourseCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function validData(array $override = []): array
    {
        return array_merge([
            'code' => 'WEB-001',
            'name' => 'Web Dasar',
            'description' => 'Belajar HTML dan CSS.',
            'fee' => 300000,
            'quota' => 20,
            'start_date' => '2026-12-01',
            'status' => 'open',
        ], $override);
    }

    // ---------- Show / route model binding ----------

    public function test_show_menampilkan_detail_course(): void
    {
        $course = Course::factory()->create(['code' => 'SHOW-01', 'name' => 'Kursus Detail']);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('SHOW-01')
            ->assertSee('Kursus Detail');
    }

    public function test_show_id_tidak_ada_menghasilkan_404(): void
    {
        $this->get('/courses/999999')->assertNotFound();
    }

    // ---------- Test matrix A ----------

    public function test_01_duplicate_code_saat_create_ditolak(): void
    {
        Course::factory()->create(['code' => 'WEB-001']);

        $this->post(route('courses.store'), $this->validData(['code' => 'WEB-001']))
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('courses', 1);
    }

    public function test_02_nama_kosong_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['name' => '']))
            ->assertSessionHasErrors('name');
    }

    public function test_03_nama_terlalu_pendek_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['name' => 'AI']))
            ->assertSessionHasErrors('name');
    }

    public function test_04_fee_negatif_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['fee' => -1000]))
            ->assertSessionHasErrors('fee');
    }

    public function test_05_fee_bukan_integer_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['fee' => 'abc']))
            ->assertSessionHasErrors('fee');
    }

    public function test_06_quota_nol_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['quota' => 0]))
            ->assertSessionHasErrors('quota');
    }

    public function test_07_quota_501_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['quota' => 501]))
            ->assertSessionHasErrors('quota');
    }

    public function test_08_quota_minimum_1_diterima(): void
    {
        $this->post(route('courses.store'), $this->validData(['quota' => 1]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', ['code' => 'WEB-001', 'quota' => 1]);
    }

    // ---------- Test matrix B ----------

    public function test_09_quota_maksimum_500_diterima(): void
    {
        $this->post(route('courses.store'), $this->validData(['quota' => 500]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('courses', ['code' => 'WEB-001', 'quota' => 500]);
    }

    public function test_10_tanggal_invalid_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['start_date' => 'bukan-tanggal']))
            ->assertSessionHasErrors('start_date');
    }

    public function test_11_status_invalid_ditolak(): void
    {
        $this->post(route('courses.store'), $this->validData(['status' => 'active']))
            ->assertSessionHasErrors('status');
    }

    public function test_12_update_dengan_code_sendiri_berhasil(): void
    {
        $course = Course::factory()->create(['code' => 'WEB-001', 'name' => 'Nama Lama']);

        $this->put(route('courses.update', $course), $this->validData([
            'code' => 'WEB-001',
            'name' => 'Nama Baru',
        ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('courses.show', $course));

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'name' => 'Nama Baru']);
    }

    public function test_13_update_ke_code_course_lain_ditolak(): void
    {
        Course::factory()->create(['code' => 'LAIN-01']);
        $course = Course::factory()->create(['code' => 'WEB-001']);

        $this->put(route('courses.update', $course), $this->validData(['code' => 'LAIN-01']))
            ->assertSessionHasErrors('code');

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'code' => 'WEB-001']);
    }

    public function test_14_update_normal_menampilkan_data_baru_di_detail(): void
    {
        $course = Course::factory()->create(['code' => 'WEB-001']);

        $this->followingRedirects()
            ->put(route('courses.update', $course), $this->validData([
                'name' => 'Web Lanjutan',
                'fee' => 450000,
                'quota' => 30,
                'status' => 'closed',
            ]))
            ->assertOk()
            ->assertSee('Kursus berhasil diperbarui.')
            ->assertSee('Web Lanjutan');
    }

    public function test_15_delete_menghapus_data_dan_flash_sukses(): void
    {
        $course = Course::factory()->create(['code' => 'DEL-001']);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success', 'Kursus berhasil dihapus.');

        $this->assertDatabaseMissing('courses', ['code' => 'DEL-001']);
    }

    // ---------- Edit view ----------

    public function test_edit_menampilkan_nilai_database(): void
    {
        $course = Course::factory()->create(['code' => 'EDT-001', 'name' => 'Kursus Edit']);

        $this->get(route('courses.edit', $course))
            ->assertOk()
            ->assertSee('value="EDT-001"', false)
            ->assertSee('value="Kursus Edit"', false);
    }

    public function test_input_lama_dipertahankan_saat_validasi_update_gagal(): void
    {
        $course = Course::factory()->create(['code' => 'WEB-001', 'name' => 'Nama Database']);

        $this->from(route('courses.edit', $course))
            ->put(route('courses.update', $course), $this->validData(['name' => 'AI', 'quota' => 0]))
            ->assertRedirect(route('courses.edit', $course))
            ->assertSessionHasErrors(['name', 'quota']);

        $this->followingRedirects()
            ->from(route('courses.edit', $course))
            ->put(route('courses.update', $course), $this->validData(['name' => 'AI']))
            ->assertSee('value="AI"', false);
    }
}
