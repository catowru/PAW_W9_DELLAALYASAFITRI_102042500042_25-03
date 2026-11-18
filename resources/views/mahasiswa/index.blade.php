@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')

@section('content')
<h1>Dashboard Mahasiswa</h1>
<p class="sub">Halaman ini dilindungi <code>role:mahasiswa</code>.</p>

<div class="grid">
    <div class="stat"><b>{{ $jumlahMatkul }}</b><span>Mata kuliah bernilai</span></div>
    <div class="stat"><b>{{ $rataRata ?? '—' }}</b><span>Rata-rata nilai</span></div>
    <div class="stat"><b>{{ $jumlahMateri }}</b><span>Materi tersedia</span></div>
</div>

<div class="card" style="margin-top:18px">
    <div class="row">
        <a class="btn" href="{{ route('nilai.saya') }}">Lihat Nilai Saya</a>
        <a class="btn ghost" href="{{ route('materi.index') }}">Lihat Materi</a>
    </div>
</div>
@endsection
