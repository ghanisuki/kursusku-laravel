@extends('layouts.app')

@section('title', 'Tambah Kursus - KursusKu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tambah Kursus</h1>
    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Kembali</a>
</div>

<form method="POST" action="{{ route('courses.store') }}" class="card shadow-sm" novalidate>
    @csrf

    @include('courses._form', ['course' => null])

    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>
@endsection
