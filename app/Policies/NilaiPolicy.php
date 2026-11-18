<?php

namespace App\Policies;

use App\Models\Nilai;
use App\Models\User;

/**
 * Middleware mengamankan JALUR (route); Policy mengamankan DATA.
 * Contoh: mahasiswa boleh membuka halaman nilai, tetapi hanya nilai miliknya sendiri.
 */
class NilaiPolicy
{
    public function view(User $user, Nilai $nilai): bool
    {
        if ($user->hasRole(User::ROLE_ADMIN, User::ROLE_DOSEN)) {
            return true;
        }

        return $nilai->mahasiswa_id === $user->id;
    }
}
