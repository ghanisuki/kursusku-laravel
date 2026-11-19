@extends('layouts.app')

@section('title', 'Tambah Enrollment - KursusKu')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Tambah Enrollment</h1>
            <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">Laporan</a>
        </div>

        <form method="POST" action="{{ route('enrollments.store') }}" class="card shadow-sm" novalidate>
            @csrf

            <div class="card-body">
                @if ($participants->isEmpty())
                    <div class="alert alert-warning">
                        Belum ada participant. Jalankan
                        <code>php artisan db:seed --class=ParticipantSeeder</code>
                        atau tambahkan lewat Tinker (Langkah 5).
                    </div>
                @endif

                @if ($courses->isEmpty())
                    <div class="alert alert-warning">
                        Belum ada course berstatus <strong>open</strong>. Ubah status course lewat menu Kursus.
                    </div>
                @endif

                <div class="mb-3">
                    <label for="participant_id" class="form-label">Participant</label>
                    <select name="participant_id" id="participant_id"
                            class="form-select @error('participant_id') is-invalid @enderror">
                        <option value="">-- Pilih Participant --</option>
                        @foreach ($participants as $participant)
                            <option value="{{ $participant->id }}"
                                @selected(old('participant_id') == $participant->id)>
                                {{ $participant->name }} - {{ $participant->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('participant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="course_id" class="form-label">Course</label>
                    <select name="course_id" id="course_id"
                            class="form-select @error('course_id') is-invalid @enderror">
                        <option value="">-- Pilih Course --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                @selected(old('course_id') == $course->id)>
                                {{ $course->code }} - {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Diisi oleh jQuery Ajax (resources/js/app.js) saat course dipilih --}}
                <div id="availability" class="alert alert-secondary mb-0">
                    Pilih kursus untuk melihat sisa kursi.
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan Enrollment</button>
            </div>
        </form>
    </div>
</div>
@endsection
