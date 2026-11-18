@extends('layouts.app')
@section('title', 'Kelola Nilai')

@section('content')
<div class="head">
    <div>
        <h1>Kelola Nilai</h1>
        <p class="sub" style="margin:0">Admin &amp; Dosen (<code>role:admin,dosen</code>).</p>
    </div>
    <a class="btn" href="{{ route('kelola-nilai.create') }}">+ Input Nilai</a>
</div>

<div class="card">
    <table>
        <tr><th>Mahasiswa</th><th>Mata Kuliah</th><th>Nilai</th><th>Huruf</th><th>Diinput oleh</th><th></th></tr>
        @forelse($daftarNilai as $n)
            <tr>
                <td>{{ $n->mahasiswa?->name ?? '—' }}</td>
                <td>{{ $n->mata_kuliah }}</td>
                <td>{{ $n->nilai }}</td>
                <td><b>{{ $n->huruf }}</b></td>
                <td>{{ $n->dosen?->name ?? '—' }}</td>
                <td><div class="row end">
                    <a class="btn ghost sm" href="{{ route('kelola-nilai.edit', $n) }}">Edit</a>
                    <form class="inline" method="POST" action="{{ route('kelola-nilai.destroy', $n) }}"
                          onsubmit="return confirm('Hapus nilai ini?')">
                        @csrf @method('DELETE')
                        <button class="btn danger sm" type="submit">Hapus</button>
                    </form>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Belum ada data nilai.</td></tr>
        @endforelse
    </table>
</div>
@endsection
