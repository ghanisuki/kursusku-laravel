@extends('layouts.app')

@section('title', 'Login - KursusKu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-1">Selamat datang 👋</h1>
                <p class="text-muted mb-4">Masuk dengan username atau email untuk melanjutkan.</p>

                <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="login" class="form-label fw-semibold">Username / Email</label>
                        <input type="text" id="login" name="login" value="{{ old('login') }}"
                               class="form-control @error('login') is-invalid @enderror"
                               placeholder="Masukkan username atau email"
                               autocomplete="username" autofocus required>
                        @error('login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Masukkan password"
                                   autocomplete="current-password" required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#password">Lihat</button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1"
                                   @checked(old('remember'))>
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="small">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Masuk</button>
                </form>

                <p class="text-center small mt-4 mb-1">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
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
