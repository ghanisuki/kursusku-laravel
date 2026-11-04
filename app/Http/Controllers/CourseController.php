<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::latest()->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:courses,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fee' => 'required|integer|min:0',
            'quota' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'status' => 'required|in:draft,open,closed',
        ]);

        Course::create($data);

        return redirect()->route('courses.index')
            ->with('success', 'Kursus berhasil ditambahkan.');
    }
}