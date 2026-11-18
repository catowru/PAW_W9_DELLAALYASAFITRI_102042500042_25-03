@extends('layouts.app')
@section('title', $materi->judul)

@section('content')
<p><a href="{{ route('materi.index') }}">← Kembali ke daftar materi</a></p>
<div class="card">
    <h1>{{ $materi->judul }}</h1>
    <div class="muted small" style="margin-bottom:14px">{{ $materi->mata_kuliah }} · oleh {{ $materi->pembuat?->name ?? '—' }} · {{ $materi->created_at->format('d M Y') }}</div>
    <div class="pre">{{ $materi->isi }}</div>
</div>
@endsection
