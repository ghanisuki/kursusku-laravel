<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    /**
     * Laporan relasional: participant + course + start date + status.
     * with() = eager loading, supaya relasi dimuat sekali (bukan satu query per baris).
     */
    public function index()
    {
        $enrollments = Enrollment::with(['participant', 'course'])
            ->latest()
            ->get();

        return view('enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $participants = Participant::orderBy('name')->get();

        // Status course di proyek ini: draft / open / closed.
        // Hanya course "open" yang bisa dipilih untuk pendaftaran.
        $courses = Course::where('status', 'open')
            ->orderBy('name')
            ->get();

        return view('enrollments.create', compact('participants', 'courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'participant_id' => ['required', 'exists:participants,id'],
                'course_id' => [
                    'required',
                    'exists:courses,id',
                    // unique pada PASANGAN participant + course, bukan pada kolom tunggal
                    Rule::unique('enrollments')->where(
                        fn ($query) => $query->where('participant_id', $request->participant_id)
                    ),
                ],
            ],
            [
                'participant_id.required' => 'Participant wajib dipilih.',
                'participant_id.exists' => 'Participant yang dipilih tidak ditemukan.',
                'course_id.required' => 'Course wajib dipilih.',
                'course_id.exists' => 'Course yang dipilih tidak ditemukan.',
                'course_id.unique' => 'Participant ini sudah terdaftar pada course tersebut.',
            ]
        );

        $data['status'] = 'registered';

        Enrollment::create($data);

        return to_route('enrollments.index')
            ->with('success', 'Enrollment berhasil disimpan.');
    }
}
