@extends('layouts.app')
@section('title', '403 Akses Ditolak')

@section('content')
<div class="err403">
    <div class="code">403</div>
    <h1>Akses ditolak</h1>
    <p class="sub">{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses!' }}</p>
    @auth
        <p class="muted small">Anda login sebagai <b>{{ auth()->user()->email }}</b> ({{ auth()->user()->roleLabel() }}).</p>
        <a class="btn" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
    @else
        <a class="btn" href="{{ route('login') }}">Ke halaman login</a>
    @endauth
</div>
@endsection
