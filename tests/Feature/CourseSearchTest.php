<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pertemuan 13: search q, filter status, pagination 10 item, partial (Ajax).
 * Jalankan: php artisan test --filter=CourseSearchTest
 */
class CourseSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function course(string $name, string $status = 'open', ?string $code = null): Course
    {
        return Course::factory()->create(array_filter([
            'name' => $name,
            'status' => $status,
            'code' => $code,
        ]));
    }

    private function index(array $query = [])
    {
        return $this->get(route('courses.index', $query));
    }

    // ---------- 5 skenario utama (Langkah 12) ----------

    public function test_skenario_1_nol_hasil_menampilkan_empty_state(): void
    {
        $this->course('Laravel Dasar');

        $response = $this->index(['q' => 'tidak-ada-xyz-999']);

        $response->assertOk()
            ->assertSee('Tidak ada kursus yang sesuai pencarian/filter.');
        $this->assertSame(0, $response->viewData('courses')->total());
    }

    public function test_skenario_2_satu_hasil(): void
    {
        $this->course('Pencarian Unik Zenith');
        $this->course('Laravel Dasar');
        $this->course('MySQL Dasar');

        $response = $this->index(['q' => 'zenith']);

        $response->assertOk()->assertSee('Pencarian Unik Zenith');
        $this->assertSame(1, $response->viewData('courses')->total());
    }

    public function test_skenario_3_lebih_dari_10_hasil_dipaginasi_10_per_halaman(): void
    {
        Course::factory()->count(25)->create();

        $page1 = $this->index();
        $paginator = $page1->viewData('courses');

        $page1->assertOk()->assertSee('pagination', false);
        $this->assertSame(10, $paginator->count());
        $this->assertSame(25, $paginator->total());
        $this->assertSame(3, $paginator->lastPage());

        $this->assertSame(10, $this->index(['page' => 2])->viewData('courses')->count());
        $this->assertSame(5, $this->index(['page' => 3])->viewData('courses')->count());
    }

    public function test_skenario_4_keyword_dengan_spasi_terbaca_benar(): void
    {
        $this->course('Web Modern Dasar');
        $this->course('Web Modern Lanjutan');
        $this->course('Web Dasar Biasa');

        $this->assertSame(2, $this->index(['q' => 'Web Modern'])->viewData('courses')->total());

        // spasi di awal/akhir dibuang (trim)
        $this->assertSame(2, $this->index(['q' => '  Web Modern  '])->viewData('courses')->total());
    }

    public function test_skenario_5_filter_dan_page_2_mempertahankan_query_string(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->course(sprintf('Modern Kelas %02d', $i), 'open');
        }
        $this->course('Modern Kelas Tertutup', 'closed');

        $response = $this->index(['q' => 'Modern', 'status' => 'open', 'page' => 2]);
        $paginator = $response->viewData('courses');

        $response->assertOk();
        $this->assertSame(12, $paginator->total());
        $this->assertSame(2, $paginator->count());
        $this->assertTrue($paginator->getCollection()->every(fn ($c) => $c->status === 'open'));

        // link pagination masih membawa q dan status (withQueryString)
        $response->assertSee('q=Modern', false)->assertSee('status=open', false);
    }

    // ---------- Search & filter tambahan ----------

    public function test_pencarian_juga_mencocokkan_kode_course(): void
    {
        $this->course('Kursus A', 'open', 'ABC-100');
        $this->course('Kursus B', 'open', 'XYZ-200');

        $response = $this->index(['q' => 'ABC-1']);

        $this->assertSame(1, $response->viewData('courses')->total());
        $response->assertSee('ABC-100');
    }

    public function test_filter_status_hanya_menampilkan_status_itu(): void
    {
        $this->course('A', 'open');
        $this->course('B', 'closed');
        $this->course('C', 'draft');

        foreach (['open', 'closed', 'draft'] as $status) {
            $paginator = $this->index(['status' => $status])->viewData('courses');
            $this->assertSame(1, $paginator->total());
            $this->assertSame($status, $paginator->first()->status);
        }
    }

    public function test_status_kosong_atau_tidak_valid_diabaikan_bukan_nol_hasil(): void
    {
        $this->course('A', 'open');
        $this->course('B', 'closed');
        $this->course('C', 'draft');

        $this->assertSame(3, $this->index(['status' => ''])->viewData('courses')->total());
        $this->assertSame(3, $this->index(['status' => 'active'])->viewData('courses')->total());
    }

    public function test_search_dan_status_digabung(): void
    {
        $this->course('Web Modern Dasar', 'open');
        $this->course('Web Modern Lanjutan', 'closed');
        $this->course('Laravel Dasar', 'open');

        $paginator = $this->index(['q' => 'Web Modern', 'status' => 'open'])->viewData('courses');

        $this->assertSame(1, $paginator->total());
        $this->assertSame('Web Modern Dasar', $paginator->first()->name);
    }

    public function test_hasil_diurutkan_berdasarkan_nama(): void
    {
        $this->course('Beta Kelas');
        $this->course('Alpha Kelas');

        $this->assertSame('Alpha Kelas', $this->index()->viewData('courses')->first()->name);
    }

    public function test_parameter_q_berbentuk_array_tidak_menyebabkan_error(): void
    {
        $this->course('Laravel Dasar');

        $this->get('/courses?q[]=x&status[]=open')->assertOk();
    }

    // ---------- Form & fallback tanpa JavaScript ----------

    public function test_form_get_mempertahankan_nilai_input_dan_pilihan_status(): void
    {
        $this->course('Pencarian Unik Zenith');

        $this->index(['q' => 'zenith', 'status' => 'open'])
            ->assertOk()
            ->assertSee('method="GET"', false)
            ->assertSee('name="q"', false)
            ->assertSee('value="zenith"', false)
            ->assertSee('value="open" selected', false);
    }

    public function test_halaman_penuh_memiliki_layout_dan_wadah_hasil(): void
    {
        $response = $this->index();

        $response->assertOk()
            ->assertSee('<html', false)
            ->assertSee('id="live-search"', false)
            ->assertSee('id="course-results"', false);
    }

    // ---------- Partial (Ajax) ----------

    public function test_partial_hanya_mengembalikan_blok_hasil_tanpa_layout(): void
    {
        $this->course('Pencarian Unik Zenith');

        $this->index(['q' => 'zenith', 'partial' => 1])
            ->assertOk()
            ->assertSee('id="course-results"', false)
            ->assertSee('Pencarian Unik Zenith')
            ->assertDontSee('<html', false)
            ->assertDontSee('navbar', false);
    }

    public function test_link_pagination_pada_partial_tidak_membawa_partial_tetapi_membawa_filter(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->course(sprintf('Modern Kelas %02d', $i), 'open');
        }

        $this->index(['q' => 'Modern', 'status' => 'open', 'partial' => 1])
            ->assertOk()
            ->assertSee('page=2', false)
            ->assertSee('q=Modern', false)
            ->assertSee('status=open', false)
            ->assertDontSee('partial=1', false);
    }

    public function test_partial_nol_hasil_tetap_menampilkan_empty_state(): void
    {
        $this->index(['q' => 'tidak-ada-xyz-999', 'partial' => 1])
            ->assertOk()
            ->assertSee('Tidak ada kursus yang sesuai pencarian/filter.');
    }
}
