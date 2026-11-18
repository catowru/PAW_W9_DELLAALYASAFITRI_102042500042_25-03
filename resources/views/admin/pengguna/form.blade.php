@extends('layouts.app')
@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')
<h1>{{ $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h1>
<p class="sub">Role hanya dapat ditetapkan oleh Admin.</p>

<div class="card" style="max-width:560px">
    <form method="POST" action="{{ $user->exists ? route('admin.pengguna.update', $user) : route('admin.pengguna.store') }}">
        @csrf
        @if($user->exists) @method('PUT') @endif

        <label for="name">Nama</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name')<div class="err-text">{{ $message }}</div>@enderror

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="err-text">{{ $message }}</div>@enderror

        <label for="password">Password @if($user->exists)<span class="muted small">(kosongkan jika tidak diubah)</span>@endif</label>
        <input id="password" type="password" name="password" @unless($user->exists) required @endunless>
        @error('password')<div class="err-text">{{ $message }}</div>@enderror

        <label for="role">Role</label>
        <select id="role" name="role" required>
            @foreach(\App\Models\User::ROLES as $role)
                <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        @error('role')<div class="err-text">{{ $message }}</div>@enderror

        <div class="row" style="margin-top:20px">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn ghost" href="{{ route('admin.pengguna.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
