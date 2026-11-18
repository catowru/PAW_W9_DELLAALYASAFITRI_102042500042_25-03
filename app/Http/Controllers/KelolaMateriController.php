<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Kelola materi: Admin & Dosen (role:admin,dosen). */
class KelolaMateriController extends Controller
{
    public function index(): View
    {
        return view('kelola-materi.index', [
            'materi' => Materi::with('pembuat')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('kelola-materi.form', ['materi' => new Materi]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['dibuat_oleh'] = $request->user()->id;

        Materi::create($data);

        return redirect()->route('kelola-materi.index')->with('status', 'Materi berhasil ditambahkan.');
    }

    public function edit(Materi $materi): View
    {
        return view('kelola-materi.form', ['materi' => $materi]);
    }

    public function update(Request $request, Materi $materi): RedirectResponse
    {
        $materi->update($this->validated($request));

        return redirect()->route('kelola-materi.index')->with('status', 'Materi berhasil diperbarui.');
    }

    public function destroy(Materi $materi): RedirectResponse
    {
        $materi->delete();

        return redirect()->route('kelola-materi.index')->with('status', 'Materi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'mata_kuliah' => ['required', 'string', 'max:100'],
            'isi' => ['required', 'string'],
        ]);
    }
}
