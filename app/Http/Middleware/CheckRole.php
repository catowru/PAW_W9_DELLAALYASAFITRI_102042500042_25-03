<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Pemeriksaan otorisasi berbasis role (RBAC).
     *
     * Pemakaian pada route:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,dosen')   // beberapa role sekaligus
     *
     * @param  string  ...$roles  daftar role yang diizinkan
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login (autentikasi)
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Pastikan role pengguna termasuk dalam daftar role yang diizinkan (otorisasi)
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses!');
        }

        // 3. Lolos pemeriksaan -> teruskan request
        return $next($request);
    }
}
