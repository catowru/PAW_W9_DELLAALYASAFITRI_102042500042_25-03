@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php($u = auth()->user())
<h1>Halo, {{ $u->name }} 👋</h1>
<p class="sub">Anda login sebagai <span class="badge {{ $u->role }}">{{ $u->roleLabel() }}</span>. Berikut menu yang dapat Anda akses.</p>

<div class="grid menu">
    <a class="item" href="{{ route('materi.index') }}"><b>Lihat Materi</b><span>Semua role</span></a>

    @if($u->hasRole('admin'))
        <a class="item" href="{{ route('admin.index') }}"><b>Halaman Admin</b><span>Ringkasan sistem</span></a>
        <a class="item" href="{{ route('admin.pengguna.index') }}"><b>Manajemen Pengguna</b><span>Khusus Admin</span></a>
    @endif
    @if($u->hasRole('dosen'))
        <a class="item" href="{{ route('dosen.index') }}"><b>Dashboard Dosen</b><span>Khusus Dosen</span></a>
    @endif
    @if($u->hasRole('mahasiswa'))
        <a class="item" href="{{ route('mahasiswa.index') }}"><b>Dashboard Mahasiswa</b><span>Khusus Mahasiswa</span></a>
    @endif
    @if($u->hasRole('admin', 'dosen'))
        <a class="item" href="{{ route('kelola-materi.index') }}"><b>Kelola Materi</b><span>Admin &amp; Dosen</span></a>
        <a class="item" href="{{ route('kelola-nilai.index') }}"><b>Kelola Nilai</b><span>Admin &amp; Dosen</span></a>
    @endif
    @if($u->hasRole('admin', 'mahasiswa'))
        <a class="item" href="{{ route('nilai.saya') }}"><b>Nilai Saya</b><span>Admin &amp; Mahasiswa</span></a>
    @endif
</div>

<div class="card" style="margin-top:22px">
    <h2>Hak akses Anda</h2>
    <table>
        <tr><th>Fitur</th><th>Admin</th><th>Dosen</th><th>Mahasiswa</th></tr>
        @foreach([
            ['Dashboard', 1, 1, 1],
            ['Manajemen pengguna', 1, 0, 0],
            ['Kelola materi', 1, 1, 0],
            ['Lihat materi', 1, 1, 1],
            ['Kelola nilai', 1, 1, 0],
            ['Lihat nilai pribadi', 1, 0, 1],
        ] as [$fitur, $a, $d, $m])
            <tr>
                <td>{{ $fitur }}</td>
                <td>{{ $a ? '✓' : '—' }}</td><td>{{ $d ? '✓' : '—' }}</td><td>{{ $m ? '✓' : '—' }}</td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
