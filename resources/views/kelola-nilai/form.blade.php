@extends('layouts.app')
@section('title', $nilai->exists ? 'Edit Nilai' : 'Input Nilai')

@section('content')
<h1>{{ $nilai->exists ? 'Edit Nilai' : 'Input Nilai' }}</h1>
<p class="sub">&nbsp;</p>

<div class="card" style="max-width:560px">
    <form method="POST" action="{{ $nilai->exists ? route('kelola-nilai.update', $nilai) : route('kelola-nilai.store') }}">
        @csrf
        @if($nilai->exists) @method('PUT') @endif

        <label for="mahasiswa_id">Mahasiswa</label>
        <select id="mahasiswa_id" name="mahasiswa_id" required>
            <option value="">— pilih mahasiswa —</option>
            @foreach($mahasiswa as $m)
                <option value="{{ $m->id }}" @selected((int) old('mahasiswa_id', $nilai->mahasiswa_id) === $m->id)>{{ $m->name }} ({{ $m->email }})</option>
            @endforeach
        </select>
        @error('mahasiswa_id')<div class="err-text">{{ $message }}</div>@enderror

        <label for="mata_kuliah">Mata Kuliah</label>
        <input id="mata_kuliah" type="text" name="mata_kuliah" value="{{ old('mata_kuliah', $nilai->mata_kuliah) }}" required>
        @error('mata_kuliah')<div class="err-text">{{ $message }}</div>@enderror

        <label for="nilai">Nilai (0–100)</label>
        <input id="nilai" type="number" name="nilai" min="0" max="100" value="{{ old('nilai', $nilai->nilai) }}" required>
        @error('nilai')<div class="err-text">{{ $message }}</div>@enderror

        <label for="keterangan">Keterangan <span class="muted small">(opsional)</span></label>
        <input id="keterangan" type="text" name="keterangan" value="{{ old('keterangan', $nilai->keterangan) }}">
        @error('keterangan')<div class="err-text">{{ $message }}</div>@enderror

        <div class="row" style="margin-top:20px">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn ghost" href="{{ route('kelola-nilai.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
