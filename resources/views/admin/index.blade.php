@extends('layouts.app')
@section('title', 'Halaman Admin')

@section('content')
<h1>Halaman Admin</h1>
<p class="sub">Ringkasan sistem. Halaman ini dilindungi <code>role:admin</code>.</p>

<div class="grid">
    <div class="stat"><b>{{ $jumlahAdmin }}</b><span>Akun Admin</span></div>
    <div class="stat"><b>{{ $jumlahDosen }}</b><span>Akun Dosen</span></div>
    <div class="stat"><b>{{ $jumlahMahasiswa }}</b><span>Akun Mahasiswa</span></div>
    <div class="stat"><b>{{ $jumlahMateri }}</b><span>Materi</span></div>
    <div class="stat"><b>{{ $jumlahNilai }}</b><span>Data nilai</span></div>
</div>

<div class="card" style="margin-top:18px">
    <div class="row">
        <a class="btn" href="{{ route('admin.pengguna.index') }}">Manajemen Pengguna</a>
        <a class="btn ghost" href="{{ route('kelola-materi.index') }}">Kelola Materi</a>
        <a class="btn ghost" href="{{ route('kelola-nilai.index') }}">Kelola Nilai</a>
    </div>
</div>
@endsection
