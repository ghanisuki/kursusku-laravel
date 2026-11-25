<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Daftar kursus dengan pencarian (q), filter status, dan pagination 10 item.
     * Satu query dipakai untuk halaman penuh dan untuk respons partial (Ajax).
     */
    public function index(Request $request)
    {
        $search = $this->queryString($request, 'q');
        $status = $this->queryString($request, 'status');
        $allowedStatuses = ['open', 'closed', 'draft'];

        $courses = Course::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            // Filter status hanya dipakai jika nilainya dikenal; kosong/tidak valid = semua status.
            ->when(
                in_array($status, $allowedStatuses, true),
                fn ($query) => $query->where('status', $status)
            )
            ->orderBy('name')
            ->orderBy('id') // pembeda agar urutan antar halaman stabil
            ->paginate(10)
            ->withQueryString();

        if ($request->boolean('partial')) {
            // Link pagination pada respons partial tidak membawa partial=1,
            // supaya tidak membuka fragmen HTML jika link dibuka langsung.
            $courses->appends(['partial' => null]);

            return view('courses._results', compact('courses'));
        }

        return view('courses.index', compact('courses', 'search', 'status'));
    }

    /**
     * Ambil parameter query sebagai string yang sudah di-trim.
     * Jika dikirim sebagai array (?q[]=x), dianggap kosong agar tidak error.
     */
    private function queryString(Request $request, string $key): string
    {
        $value = $request->query($key);

        return is_string($value) ? trim($value) : '';
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(StoreCourseRequest $request)
    {
        Course::create($request->validated());

        return to_route('courses.index')
            ->with('success', 'Kursus berhasil ditambahkan.');
    }

    public function show(Course $course)
    {
        // Peserta terdaftar diambil lewat relasi Course -> enrollments -> participant.
        $course->load('enrollments.participant');

        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return to_route('courses.show', $course)
            ->with('success', 'Kursus berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return to_route('courses.index')
            ->with('success', 'Kursus berhasil dihapus.');
    }

    /**
     * JSON sisa kursi untuk Ajax pada form enrollment (pertemuan 9),
     * sekarang dihitung dari database: quota - jumlah enrollment.
     */
    public function availability(Course $course)
    {
        $course->loadCount('enrollments');

        return response()->json([
            'course' => $course->name,
            'remaining' => max(0, $course->quota - $course->enrollments_count),
        ]);
    }
}
