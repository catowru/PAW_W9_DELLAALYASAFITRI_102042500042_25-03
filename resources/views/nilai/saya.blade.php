@extends('layouts.app')
@section('title', 'Nilai Saya')

@section('content')
<h1>Nilai Saya</h1>
<p class="sub">Hanya menampilkan nilai milik akun Anda sendiri (<code>role:admin,mahasiswa</code>).</p>

<div class="card">
    <table>
        <tr><th>Mata Kuliah</th><th>Nilai</th><th>Huruf</th><th>Keterangan</th><th></th></tr>
        @forelse($daftarNilai as $n)
            <tr>
                <td>{{ $n->mata_kuliah }}</td>
                <td>{{ $n->nilai }}</td>
                <td><b>{{ $n->huruf }}</b></td>
                <td>{{ $n->keterangan ?? '—' }}</td>
                <td><div class="row end"><a class="btn ghost sm" href="{{ route('nilai.show', $n) }}">Detail</a></div></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Belum ada nilai untuk akun ini.</td></tr>
        @endforelse
    </table>
</div>
@endsection
