@extends('layouts.app')

@section('title', 'Atur Ulang Password - KursusKu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-1">Atur ulang password</h1>
                <p class="text-muted mb-4">Buat password baru untuk akun Anda.</p>

                <form method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password baru</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password" autofocus required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#password">Lihat</button>
                        </div>
                        <div class="form-text">Minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.</div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Ulangi password baru</label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#password_confirmation">Lihat</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Simpan password baru</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('partials.password-toggle')
@endsection
