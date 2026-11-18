<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Melihat nilai pribadi. */
class NilaiController extends Controller
{
    /** /nilai-saya -> role:admin,mahasiswa; data dibatasi ke akun yang sedang login. */
    public function saya(Request $request): View
    {
        return view('nilai.saya', [
            'daftarNilai' => $request->user()->nilai()->latest()->get(),
        ]);
    }

    /** /nilai/{nilai} -> auth + NilaiPolicy (mahasiswa hanya boleh membuka nilai miliknya). */
    public function show(Nilai $nilai): View
    {
        Gate::authorize('view', $nilai);

        return view('nilai.show', ['nilai' => $nilai->load(['mahasiswa', 'dosen'])]);
    }
}
