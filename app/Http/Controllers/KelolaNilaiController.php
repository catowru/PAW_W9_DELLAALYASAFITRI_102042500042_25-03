<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Kelola nilai: Admin & Dosen (role:admin,dosen). */
class KelolaNilaiController extends Controller
{
    public function index(): View
    {
        return view('kelola-nilai.index', [
            'daftarNilai' => Nilai::with(['mahasiswa', 'dosen'])->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('kelola-nilai.form', [
            'nilai' => new Nilai,
            'mahasiswa' => $this->daftarMahasiswa(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['dosen_id'] = $request->user()->id;

        Nilai::create($data);

        return redirect()->route('kelola-nilai.index')->with('status', 'Nilai berhasil disimpan.');
    }

    public function edit(Nilai $nilai): View
    {
        return view('kelola-nilai.form', [
            'nilai' => $nilai,
            'mahasiswa' => $this->daftarMahasiswa(),
        ]);
    }

    public function update(Request $request, Nilai $nilai): RedirectResponse
    {
        $nilai->update($this->validated($request));

        return redirect()->route('kelola-nilai.index')->with('status', 'Nilai berhasil diperbarui.');
    }

    public function destroy(Nilai $nilai): RedirectResponse
    {
        $nilai->delete();

        return redirect()->route('kelola-nilai.index')->with('status', 'Nilai berhasil dihapus.');
    }

    private function daftarMahasiswa()
    {
        return User::where('role', User::ROLE_MAHASISWA)->orderBy('name')->get();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            // hanya akun ber-role mahasiswa yang boleh diberi nilai
            'mahasiswa_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_MAHASISWA)],
            'mata_kuliah' => ['required', 'string', 'max:100'],
            'nilai' => ['required', 'integer', 'between:0,100'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
