@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="login">
    <h1>Akademik App</h1>
    <p class="sub">Silakan login untuk melanjutkan</p>

    <div class="card">
        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')<div class="err-text">{{ $message }}</div>@enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
            @error('password')<div class="err-text">{{ $message }}</div>@enderror

            <label style="font-weight:400"><input type="checkbox" name="remember" value="1"> Ingat saya</label>

            <div style="margin-top:16px"><button class="btn" style="width:100%" type="submit">Login</button></div>
        </form>
    </div>

    <div class="card demo">
        <b>Akun uji (password: <code>password</code>)</b>
        <table>
            <tr><td><span class="badge admin">Admin</span></td><td><code>admin@akademik.test</code></td></tr>
            <tr><td><span class="badge dosen">Dosen</span></td><td><code>dosen@akademik.test</code></td></tr>
            <tr><td><span class="badge mahasiswa">Mahasiswa</span></td><td><code>mahasiswa@akademik.test</code></td></tr>
        </table>
    </div>
</div>
@endsection
