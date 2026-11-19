@extends('layouts.app')

@section('title', 'Detail Kursus - KursusKu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-1">Detail Kursus</h1>
        <p class="text-muted mb-0">{{ $course->code }} - {{ $course->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Kembali</a>
        <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary">Edit</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Kode</dt>
            <dd class="col-sm-9">{{ $course->code }}</dd>

            <dt class="col-sm-3">Nama</dt>
            <dd class="col-sm-9">{{ $course->name }}</dd>

            <dt class="col-sm-3">Deskripsi</dt>
            <dd class="col-sm-9">{{ $course->description ?: '-' }}</dd>

            <dt class="col-sm-3">Biaya</dt>
            <dd class="col-sm-9">Rp {{ number_format($course->fee, 0, ',', '.') }}</dd>

            <dt class="col-sm-3">Kuota</dt>
            <dd class="col-sm-9">{{ $course->quota }} peserta</dd>

            <dt class="col-sm-3">Tanggal Mulai</dt>
            <dd class="col-sm-9">{{ $course->start_date?->format('d-m-Y') ?? '-' }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">{{ ucfirst($course->status) }}</dd>
        </dl>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Peserta Terdaftar ({{ $course->enrollments->count() }} dari {{ $course->quota }} kursi)</span>
        <a href="{{ route('enrollments.create') }}" class="btn btn-sm btn-success">Tambah Enrollment</a>
    </div>
    <ul class="list-group list-group-flush">
        @forelse ($course->enrollments as $enrollment)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ $enrollment->participant->name }}
                    <span class="text-muted small">{{ $enrollment->participant->email }}</span></span>
                <span class="badge text-bg-success">{{ $enrollment->status }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted">Belum ada peserta.</li>
        @endforelse
    </ul>
</div>
@endsection
