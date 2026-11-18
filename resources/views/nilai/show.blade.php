@extends('layouts.app')
@section('title', 'Detail Nilai')

@section('content')
<p><a href="{{ url()->previous() }}">← Kembali</a></p>
<div class="card" style="max-width:560px">
    <h1>{{ $nilai->mata_kuliah }}</h1>
    <table>
        <tr><th>Mahasiswa</th><td>{{ $nilai->mahasiswa?->name }}</td></tr>
        <tr><th>Nilai</th><td>{{ $nilai->nilai }} ({{ $nilai->huruf }})</td></tr>
        <tr><th>Diinput oleh</th><td>{{ $nilai->dosen?->name ?? '—' }}</td></tr>
        <tr><th>Keterangan</th><td>{{ $nilai->keterangan ?? '—' }}</td></tr>
    </table>
    <p class="muted small" style="margin-bottom:0">Akses ke halaman ini dibatasi oleh <code>NilaiPolicy</code>.</p>
</div>
@endsection
