@extends('layouts.app')
@section('title', 'Materi')

@section('content')
<div class="head">
    <div>
        <h1>Materi Perkuliahan</h1>
        <p class="sub" style="margin:0">Dapat dilihat oleh semua role yang sudah login.</p>
    </div>
    @if(auth()->user()->hasRole('admin', 'dosen'))
        <a class="btn" href="{{ route('kelola-materi.index') }}">Kelola Materi</a>
    @endif
</div>

@forelse($materi as $m)
    <div class="card">
        <h2><a href="{{ route('materi.show', $m) }}">{{ $m->judul }}</a></h2>
        <div class="muted small">{{ $m->mata_kuliah }} · oleh {{ $m->pembuat?->name ?? '—' }} · {{ $m->created_at->format('d M Y') }}</div>
        <p style="margin-bottom:0">{{ \Illuminate\Support\Str::limit($m->isi, 160) }}</p>
    </div>
@empty
    <div class="card muted">Belum ada materi.</div>
@endforelse
@endsection
