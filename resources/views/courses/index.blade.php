@extends('layouts.app')

@section('title', 'Daftar Kursus - KursusKu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Daftar Kursus</h1>
    <a href="{{ route('courses.create') }}" class="btn btn-success">Tambah Kursus</a>
</div>

{{-- Form GET: bekerja tanpa JavaScript. Tidak butuh @csrf karena tidak mengubah data. --}}
<form id="course-filter" method="GET" action="{{ route('courses.index') }}" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="search" id="live-search" name="q" value="{{ $search }}"
               class="form-control" placeholder="Cari kode atau nama kursus"
               autocomplete="off">
    </div>
    <div class="col-md-3">
        <select id="status-filter" name="status" class="form-select">
            <option value="">Semua status</option>
            <option value="open" @selected($status === 'open')>Open</option>
            <option value="closed" @selected($status === 'closed')>Closed</option>
            <option value="draft" @selected($status === 'draft')>Draft</option>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

@include('courses._results')
@endsection
