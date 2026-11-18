<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Dashboard umum: boleh diakses semua role yang sudah login. */
    public function index(): View
    {
        return view('dashboard');
    }

    /** /admin -> role:admin */
    public function admin(): View
    {
        return view('admin.index', [
            'jumlahAdmin' => User::where('role', User::ROLE_ADMIN)->count(),
            'jumlahDosen' => User::where('role', User::ROLE_DOSEN)->count(),
            'jumlahMahasiswa' => User::where('role', User::ROLE_MAHASISWA)->count(),
            'jumlahMateri' => Materi::count(),
            'jumlahNilai' => Nilai::count(),
        ]);
    }

    /** /dosen -> role:dosen */
    public function dosen(Request $request): View
    {
        return view('dosen.index', [
            'materiSaya' => Materi::where('dibuat_oleh', $request->user()->id)->count(),
            'nilaiDiinput' => Nilai::where('dosen_id', $request->user()->id)->count(),
            'jumlahMahasiswa' => User::where('role', User::ROLE_MAHASISWA)->count(),
        ]);
    }

    /** /mahasiswa -> role:mahasiswa */
    public function mahasiswa(Request $request): View
    {
        $nilai = $request->user()->nilai;

        return view('mahasiswa.index', [
            'jumlahMatkul' => $nilai->count(),
            'rataRata' => $nilai->isEmpty() ? null : round($nilai->avg('nilai'), 1),
            'jumlahMateri' => Materi::count(),
        ]);
    }
}
