@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@section('content')
<h1>Dashboard Dosen</h1>
<p class="sub">Halaman ini dilindungi <code>role:dosen</code>.</p>

<div class="grid">
    <div class="stat"><b>{{ $materiSaya }}</b><span>Materi yang Anda buat</span></div>
    <div class="stat"><b>{{ $nilaiDiinput }}</b><span>Nilai yang Anda input</span></div>
    <div class="stat"><b>{{ $jumlahMahasiswa }}</b><span>Mahasiswa terdaftar</span></div>
</div>

<div class="card" style="margin-top:18px">
    <div class="row">
        <a class="btn" href="{{ route('kelola-materi.index') }}">Kelola Materi</a>
        <a class="btn ghost" href="{{ route('kelola-nilai.index') }}">Kelola Nilai</a>
    </div>
</div>
@endsection
