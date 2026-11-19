@extends('layouts.app')

@section('title', 'Laporan Enrollment - KursusKu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Laporan Enrollment KursusKu</h1>
    <a href="{{ route('enrollments.create') }}" class="btn btn-success">Tambah Enrollment</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered align-middle bg-white">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Participant</th>
                <th>Course</th>
                <th>Start Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($enrollments as $enrollment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $enrollment->participant->name }}
                        <div class="small text-muted">{{ $enrollment->participant->email }}</div>
                    </td>
                    <td>
                        {{ $enrollment->course->name }}
                        <div class="small text-muted">{{ $enrollment->course->code }}</div>
                    </td>
                    <td>{{ $enrollment->course->start_date?->format('d-m-Y') ?? '-' }}</td>
                    <td><span class="badge text-bg-success">{{ $enrollment->status }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada enrollment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="text-muted small mb-0">
    Total: {{ $enrollments->count() }} enrollment. Nama participant dan course dibaca lewat relasi,
    bukan disimpan ulang di tabel enrollments.
</p>
@endsection
