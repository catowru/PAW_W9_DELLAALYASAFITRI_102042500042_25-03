<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\View\View;

/** Melihat materi: semua role yang sudah login. */
class MateriController extends Controller
{
    public function index(): View
    {
        return view('materi.index', [
            'materi' => Materi::with('pembuat')->latest()->get(),
        ]);
    }

    public function show(Materi $materi): View
    {
        return view('materi.show', ['materi' => $materi->load('pembuat')]);
    }
}
