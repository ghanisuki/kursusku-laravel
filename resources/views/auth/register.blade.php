@extends('layouts.app')

@section('title', 'Daftar - KursusKu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-1">Buat akun baru</h1>
                <p class="text-muted mb-4">Isi data berikut untuk mendaftar di KursusKu.</p>

                <form method="POST" action="{{ route('register.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               autocomplete="name" autofocus required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                               class="form-control @error('username') is-invalid @enderror"
                               autocomplete="username" required>
                        <div class="form-text">Huruf, angka, strip (-) atau garis bawah (_). Minimal 3 karakter.</div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               autocomplete="email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#password">Lihat</button>
                        </div>
                        <div class="form-text">Minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.</div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Ulangi password</label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#password_confirmation">Lihat</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Daftar</button>
                </form>

                <p class="text-center small mt-4 mb-1">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                </p>
                <p class="text-center small mb-0">
                    <a href="{{ route('home') }}">&larr; Kembali ke Beranda</a>
                </p>
            </div>
        </div>
    </div>
</div>

@include('partials.password-toggle')
@endsection
