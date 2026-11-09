@extends('layouts.app')

@section('title', 'Edit Kursus - KursusKu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Edit Kursus</h1>
    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Batal</a>
</div>

<form method="POST" action="{{ route('courses.update', $course) }}" class="card shadow-sm" novalidate>
    @csrf
    @method('PUT')

    @include('courses._form', ['course' => $course])

    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
@endsection
