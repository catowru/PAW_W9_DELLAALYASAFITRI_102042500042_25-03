<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Akademik App</title>
    <link rel="stylesheet" href="{{ asset('css/akademik.css') }}">
</head>
<body>
@auth
    @php($u = auth()->user())
    <nav class="nav">
        <div class="in">
            <a class="brand" href="{{ route('dashboard') }}">Akademik App</a>

            <a class="link {{ request()->routeIs('dashboard') ? 'on' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="link {{ request()->routeIs('materi.*') ? 'on' : '' }}" href="{{ route('materi.index') }}">Materi</a>

            {{-- Menu hanya tampilan. Keamanan sebenarnya ada pada middleware di routes/web.php --}}
            @if($u->hasRole('admin'))
                <a class="link {{ request()->routeIs('admin.*') ? 'on' : '' }}" href="{{ route('admin.index') }}">Admin</a>
            @endif
            @if($u->hasRole('dosen'))
                <a class="link {{ request()->routeIs('dosen.*') ? 'on' : '' }}" href="{{ route('dosen.index') }}">Dosen</a>
            @endif
            @if($u->hasRole('mahasiswa'))
                <a class="link {{ request()->routeIs('mahasiswa.*') ? 'on' : '' }}" href="{{ route('mahasiswa.index') }}">Mahasiswa</a>
            @endif
            @if($u->hasRole('admin', 'dosen'))
                <a class="link {{ request()->routeIs('kelola-materi.*') ? 'on' : '' }}" href="{{ route('kelola-materi.index') }}">Kelola Materi</a>
                <a class="link {{ request()->routeIs('kelola-nilai.*') ? 'on' : '' }}" href="{{ route('kelola-nilai.index') }}">Kelola Nilai</a>
            @endif
            @if($u->hasRole('admin', 'mahasiswa'))
                <a class="link {{ request()->routeIs('nilai.saya') ? 'on' : '' }}" href="{{ route('nilai.saya') }}">Nilai Saya</a>
            @endif

            <span class="sp"></span>
            <span class="who">{{ $u->name }} <span class="badge {{ $u->role }}">{{ $u->roleLabel() }}</span></span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="btn ghost sm" type="submit">Logout</button>
            </form>
        </div>
    </nav>
@endauth

<main class="wrap">
    @if(session('status'))<div class="flash ok">{{ session('status') }}</div>@endif
    @if(session('error'))<div class="flash err">{{ session('error') }}</div>@endif
    @yield('content')
</main>
</body>
</html>
