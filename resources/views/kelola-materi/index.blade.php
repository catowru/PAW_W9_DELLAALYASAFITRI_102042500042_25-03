@extends('layouts.app')
@section('title', 'Kelola Materi')

@section('content')
<div class="head">
    <div>
        <h1>Kelola Materi</h1>
        <p class="sub" style="margin:0">Admin &amp; Dosen (<code>role:admin,dosen</code>).</p>
    </div>
    <a class="btn" href="{{ route('kelola-materi.create') }}">+ Tambah Materi</a>
</div>

<div class="card">
    <table>
        <tr><th>Judul</th><th>Mata Kuliah</th><th>Dibuat oleh</th><th></th></tr>
        @forelse($materi as $m)
            <tr>
                <td><a href="{{ route('materi.show', $m) }}">{{ $m->judul }}</a></td>
                <td>{{ $m->mata_kuliah }}</td>
                <td>{{ $m->pembuat?->name ?? '—' }}</td>
                <td><div class="row end">
                    <a class="btn ghost sm" href="{{ route('kelola-materi.edit', $m) }}">Edit</a>
                    <form class="inline" method="POST" action="{{ route('kelola-materi.destroy', $m) }}"
                          onsubmit="return confirm('Hapus materi ini?')">
                        @csrf @method('DELETE')
                        <button class="btn danger sm" type="submit">Hapus</button>
                    </form>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">Belum ada materi.</td></tr>
        @endforelse
    </table>
</div>
@endsection
