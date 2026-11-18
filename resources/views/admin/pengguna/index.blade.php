@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('content')
<div class="head">
    <div>
        <h1>Manajemen Pengguna</h1>
        <p class="sub" style="margin:0">Khusus Admin (<code>role:admin</code>).</p>
    </div>
    <a class="btn" href="{{ route('admin.pengguna.create') }}">+ Tambah Pengguna</a>
</div>

<div class="card">
    <table>
        <tr><th>Nama</th><th>Email</th><th>Role</th><th></th></tr>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }} @if($user->is(auth()->user()))<span class="muted small">(Anda)</span>@endif</td>
                <td>{{ $user->email }}</td>
                <td><span class="badge {{ $user->role }}">{{ $user->roleLabel() }}</span></td>
                <td><div class="row end">
                    <a class="btn ghost sm" href="{{ route('admin.pengguna.edit', $user) }}">Edit</a>
                    @unless($user->is(auth()->user()))
                        <form class="inline" method="POST" action="{{ route('admin.pengguna.destroy', $user) }}"
                              onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn danger sm" type="submit">Hapus</button>
                        </form>
                    @endunless
                </div></td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
