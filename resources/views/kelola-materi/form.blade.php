@extends('layouts.app')
@section('title', $materi->exists ? 'Edit Materi' : 'Tambah Materi')

@section('content')
<h1>{{ $materi->exists ? 'Edit Materi' : 'Tambah Materi' }}</h1>
<p class="sub">&nbsp;</p>

<div class="card" style="max-width:720px">
    <form method="POST" action="{{ $materi->exists ? route('kelola-materi.update', $materi) : route('kelola-materi.store') }}">
        @csrf
        @if($materi->exists) @method('PUT') @endif

        <label for="judul">Judul</label>
        <input id="judul" type="text" name="judul" value="{{ old('judul', $materi->judul) }}" required>
        @error('judul')<div class="err-text">{{ $message }}</div>@enderror

        <label for="mata_kuliah">Mata Kuliah</label>
        <input id="mata_kuliah" type="text" name="mata_kuliah" value="{{ old('mata_kuliah', $materi->mata_kuliah) }}" required>
        @error('mata_kuliah')<div class="err-text">{{ $message }}</div>@enderror

        <label for="isi">Isi Materi</label>
        <textarea id="isi" name="isi" rows="8" required>{{ old('isi', $materi->isi) }}</textarea>
        @error('isi')<div class="err-text">{{ $message }}</div>@enderror

        <div class="row" style="margin-top:20px">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn ghost" href="{{ route('kelola-materi.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
