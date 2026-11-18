<?php

namespace Database\Seeders;

use App\Models\Materi;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Akun uji (semua memakai password: "password").
     * GANTI password ini jika aplikasi dipakai di luar keperluan praktikum.
     */
    public function run(): void
    {
        $admin = $this->akun('Admin Akademik', 'admin@akademik.test', User::ROLE_ADMIN);
        $dosen = $this->akun('Dr. Budi Santoso', 'dosen@akademik.test', User::ROLE_DOSEN);
        $mhs1 = $this->akun('Siti Rahma', 'mahasiswa@akademik.test', User::ROLE_MAHASISWA);
        $mhs2 = $this->akun('Andi Pratama', 'mahasiswa2@akademik.test', User::ROLE_MAHASISWA);

        $materi = [
            ['Pengantar Middleware Laravel', 'Pemrograman Web', 'Middleware adalah lapisan perantara yang memeriksa request sebelum diteruskan ke route tujuan.'],
            ['Authentication vs Authorization', 'Pemrograman Web', 'Authentication menjawab "siapa Anda?", sedangkan authorization menjawab "apa hak Anda?".'],
            ['Role-Based Access Control (RBAC)', 'Keamanan Aplikasi Web', 'RBAC mengatur izin berdasarkan peran pengguna. Terapkan prinsip Least Privilege.'],
        ];
        foreach ($materi as [$judul, $mk, $isi]) {
            Materi::firstOrCreate(['judul' => $judul], [
                'mata_kuliah' => $mk,
                'isi' => $isi,
                'dibuat_oleh' => $dosen->id,
            ]);
        }

        $nilai = [
            [$mhs1, 'Pemrograman Web', 88],
            [$mhs1, 'Basis Data', 79],
            [$mhs2, 'Pemrograman Web', 72],
            [$mhs2, 'Basis Data', 65],
        ];
        foreach ($nilai as [$mhs, $mk, $n]) {
            Nilai::firstOrCreate(
                ['mahasiswa_id' => $mhs->id, 'mata_kuliah' => $mk],
                ['dosen_id' => $dosen->id, 'nilai' => $n],
            );
        }
    }

    private function akun(string $nama, string $email, string $role): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $nama;
        $user->password = Hash::make('password');
        $user->role = $role; // role diisi eksplisit oleh sistem, bukan lewat mass-assignment
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }
}
