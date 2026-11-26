@extends('layouts.app')

@section('title', 'Lupa Password - KursusKu')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-1">Lupa password?</h1>
                <p class="text-muted mb-4">Masukkan email akun Anda. Kami buatkan tautan untuk mengatur ulang password.</p>

                @if (session('reset_link'))
                    <div class="alert alert-info">
                        <strong>Mode lokal (belum ada pengiriman email).</strong><br>
                        Klik tautan ini untuk mengatur ulang password:
                        <a href="{{ session('reset_link') }}">Atur ulang password</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="Masukkan email akun" autocomplete="email" autofocus required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100">Buat tautan reset</button>
                </form>

                <p class="text-center small mt-4 mb-0">
                    <a href="{{ route('login') }}">&larr; Kembali ke Login</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
