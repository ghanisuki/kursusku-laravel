@extends('layouts.app')

@section('title', 'Daftar Kursus - KursusKu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Daftar Kursus</h1>
    <a href="{{ route('courses.create') }}" class="btn btn-success">Tambah Kursus</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered align-middle bg-white">
        <thead class="table-dark">
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Biaya</th>
                <th>Kuota</th>
                <th>Mulai</th>
                <th>Status</th>
                <th class="text-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td>{{ $course->code }}</td>
                    <td>{{ $course->name }}</td>
                    <td>Rp {{ number_format($course->fee, 0, ',', '.') }}</td>
                    <td>{{ $course->quota }}</td>
                    <td>{{ $course->start_date?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ ucfirst($course->status) }}</td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('courses.show', $course) }}"
                               class="btn btn-sm btn-outline-primary">Detail</a>
                            <a href="{{ route('courses.edit', $course) }}"
                               class="btn btn-sm btn-outline-warning">Edit</a>
                            <form method="POST"
                                  action="{{ route('courses.destroy', $course) }}"
                                  onsubmit="return confirm('Hapus kursus ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada data kursus.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
