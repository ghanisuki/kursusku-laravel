@extends('layouts.app')

@section('title', 'Profil Saya - KursusKu')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 mb-1">Profil Saya</h1>
        <p class="text-muted mb-0">Perbarui identitas, kontak, email login, dan password Anda.</p>
    </div>
    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Kembali ke Beranda</a>
</div>

<div class="row g-4">
    {{-- Kolom kiri: Identitas & Kontak --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Identitas &amp; Kontak</h2>

                <form method="POST" action="{{ route('profile.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email Login <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            <div class="form-text">Setelah disimpan, email baru digunakan pada login berikutnya.</div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Nomor HP</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="Contoh: 081234567890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="position" class="form-label fw-semibold">Jabatan</label>
                            <input type="text" id="position" name="position" value="{{ old('position', $user->position) }}"
                                   class="form-control @error('position') is-invalid @enderror">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="institution" class="form-label fw-semibold">Instansi / Unit</label>
                            <input type="text" id="institution" name="institution" value="{{ old('institution', $user->institution) }}"
                                   class="form-control @error('institution') is-invalid @enderror">
                            @error('institution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Alamat</label>
                            <textarea id="address" name="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mt-4">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom kanan: Informasi Akses + Ganti Password --}}
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Informasi Akses</h2>
                <dl class="row mb-0 small">
                    <dt class="col-5 fw-normal text-muted">Username</dt>
                    <dd class="col-7 text-end">{{ $user->username ?? '-' }}</dd>

                    <dt class="col-5 fw-normal text-muted">Role</dt>
                    <dd class="col-7 text-end">
                        <span class="badge {{ $user->role === 'admin' ? 'text-bg-dark' : 'text-bg-secondary' }}">{{ strtoupper($user->role) }}</span>
                    </dd>

                    <dt class="col-5 fw-normal text-muted">Status</dt>
                    <dd class="col-7 text-end">
                        <span class="badge {{ $user->status === 'active' ? 'text-bg-success' : 'text-bg-danger' }}">{{ $user->status === 'active' ? 'AKTIF' : 'NONAKTIF' }}</span>
                    </dd>

                    <dt class="col-5 fw-normal text-muted">Login terakhir</dt>
                    <dd class="col-7 text-end mb-0">
                        {{ $user->last_login_at ? $user->last_login_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 mb-1">Ganti Password</h2>
                <p class="text-muted small">Password baru minimal 8 karakter serta mengandung huruf besar-kecil, angka, dan simbol.</p>

                <form method="POST" action="{{ route('profile.password') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Password saat ini</label>
                        <div class="input-group">
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password', 'passwordChange') is-invalid @enderror"
                                   autocomplete="current-password" required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#current_password">Lihat</button>
                        </div>
                        @error('current_password', 'passwordChange')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold">Password baru</label>
                        <div class="input-group">
                            <input type="password" id="new_password" name="password"
                                   class="form-control @error('password', 'passwordChange') is-invalid @enderror"
                                   autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#new_password">Lihat</button>
                        </div>
                        @error('password', 'passwordChange')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label fw-semibold">Ulangi password baru</label>
                        <div class="input-group">
                            <input type="password" id="new_password_confirmation" name="password_confirmation"
                                   class="form-control" autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary"
                                    data-toggle-password="#new_password_confirmation">Lihat</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-outline-success w-100">Ganti Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('partials.password-toggle')
@endsection
