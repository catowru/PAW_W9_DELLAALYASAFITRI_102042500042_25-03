<?php

namespace Tests\Feature;

use App\Models\Materi;
use App\Models\Nilai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Matriks pengujian akses berbasis role (RBAC).
 * 7 skenario pertama sama persis dengan tabel "Skenario dan Hasil Pengujian" pada materi.
 */
class AksesRoleTest extends TestCase
{
    use RefreshDatabase;

    /** Membuat pengguna untuk role tertentu (null = tamu / belum login). */
    private function sebagai(?string $role): ?User
    {
        if ($role === null) {
            return null;
        }

        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        return $user;
    }

    /**
     * Skenario dari materi (slide 15) + pelengkap untuk route manajemen
     * pengguna, materi, dan nilai.
     *
     * @return array<string, array{0: ?string, 1: string, 2: int|string}>
     */
    public static function skenarioGet(): array
    {
        return [
            // ---- 7 skenario dari materi ----
            '1. Admin -> /admin'                 => ['admin', '/admin', 200],
            '2. Admin -> /kelola-materi'         => ['admin', '/kelola-materi', 200],
            '3. Dosen -> /dosen'                 => ['dosen', '/dosen', 200],
            '4. Dosen -> /admin'                 => ['dosen', '/admin', 403],
            '5. Mahasiswa -> /mahasiswa'         => ['mahasiswa', '/mahasiswa', 200],
            '6. Mahasiswa -> /kelola-materi'     => ['mahasiswa', '/kelola-materi', 403],
            '7. Tamu -> /admin'                  => [null, '/admin', 'login'],

            // ---- Manajemen pengguna (admin saja) ----
            'Admin -> /admin/pengguna'           => ['admin', '/admin/pengguna', 200],
            'Admin -> /admin/pengguna/create'    => ['admin', '/admin/pengguna/create', 200],
            'Dosen -> /admin/pengguna'           => ['dosen', '/admin/pengguna', 403],
            'Mahasiswa -> /admin/pengguna'       => ['mahasiswa', '/admin/pengguna', 403],
            'Tamu -> /admin/pengguna'            => [null, '/admin/pengguna', 'login'],

            // ---- Kelola materi (admin, dosen) ----
            'Dosen -> /kelola-materi'            => ['dosen', '/kelola-materi', 200],
            'Dosen -> /kelola-materi/create'     => ['dosen', '/kelola-materi/create', 200],
            'Mahasiswa -> /kelola-materi/create' => ['mahasiswa', '/kelola-materi/create', 403],
            'Tamu -> /kelola-materi'             => [null, '/kelola-materi', 'login'],

            // ---- Kelola nilai (admin, dosen) ----
            'Admin -> /kelola-nilai'             => ['admin', '/kelola-nilai', 200],
            'Dosen -> /kelola-nilai'             => ['dosen', '/kelola-nilai', 200],
            'Mahasiswa -> /kelola-nilai'         => ['mahasiswa', '/kelola-nilai', 403],
            'Tamu -> /kelola-nilai'              => [null, '/kelola-nilai', 'login'],

            // ---- Lihat nilai pribadi (admin, mahasiswa) ----
            'Mahasiswa -> /nilai-saya'           => ['mahasiswa', '/nilai-saya', 200],
            'Admin -> /nilai-saya'               => ['admin', '/nilai-saya', 200],
            'Dosen -> /nilai-saya'               => ['dosen', '/nilai-saya', 403],
            'Tamu -> /nilai-saya'                => [null, '/nilai-saya', 'login'],

            // ---- Halaman role lain (least privilege) ----
            'Admin -> /dosen'                    => ['admin', '/dosen', 403],
            'Mahasiswa -> /dosen'                => ['mahasiswa', '/dosen', 403],
            'Admin -> /mahasiswa'                => ['admin', '/mahasiswa', 403],
            'Dosen -> /mahasiswa'                => ['dosen', '/mahasiswa', 403],
            'Mahasiswa -> /admin'                => ['mahasiswa', '/admin', 403],

            // ---- Dashboard & materi: semua role yang login ----
            'Admin -> /dashboard'                => ['admin', '/dashboard', 200],
            'Dosen -> /dashboard'                => ['dosen', '/dashboard', 200],
            'Mahasiswa -> /dashboard'            => ['mahasiswa', '/dashboard', 200],
            'Tamu -> /dashboard'                 => [null, '/dashboard', 'login'],
            'Mahasiswa -> /materi'               => ['mahasiswa', '/materi', 200],
            'Dosen -> /materi'                   => ['dosen', '/materi', 200],
            'Tamu -> /materi'                    => [null, '/materi', 'login'],
        ];
    }

    #[DataProvider('skenarioGet')]
    public function test_akses_get_sesuai_role(?string $role, string $url, int|string $diharapkan): void
    {
        $this->sebagai($role);

        $response = $this->get($url);

        if ($diharapkan === 'login') {
            $response->assertRedirect('/login');

            return;
        }

        $response->assertStatus($diharapkan);

        if ($diharapkan === 403) {
            $response->assertSee('Anda tidak memiliki hak akses!');
        }
    }

    // ------------------------------------------------------------------
    // Route penulisan (POST/PUT/DELETE) juga harus dilindungi di server
    // ------------------------------------------------------------------

    public function test_mahasiswa_tidak_dapat_membuat_pengguna_baru(): void
    {
        $this->sebagai('mahasiswa');

        $this->post('/admin/pengguna', [
            'name' => 'Hacker', 'email' => 'h@uji.test', 'password' => 'password123', 'role' => 'admin',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'h@uji.test']);
    }

    public function test_dosen_tidak_dapat_menghapus_pengguna(): void
    {
        $this->sebagai('dosen');
        $korban = User::factory()->create();

        $this->delete("/admin/pengguna/{$korban->id}")->assertForbidden();

        $this->assertModelExists($korban);
    }

    public function test_mahasiswa_tidak_dapat_menambah_atau_mengubah_nilai(): void
    {
        $mhs = $this->sebagai('mahasiswa');
        $nilai = Nilai::factory()->create(['mahasiswa_id' => $mhs->id, 'nilai' => 50]);

        $this->post('/kelola-nilai', [
            'mahasiswa_id' => $mhs->id, 'mata_kuliah' => 'X', 'nilai' => 100,
        ])->assertForbidden();

        $this->put("/kelola-nilai/{$nilai->id}", [
            'mahasiswa_id' => $mhs->id, 'mata_kuliah' => 'X', 'nilai' => 100,
        ])->assertForbidden();

        $this->assertSame(50, $nilai->fresh()->nilai);
    }

    public function test_mahasiswa_tidak_dapat_menghapus_materi(): void
    {
        $this->sebagai('mahasiswa');
        $materi = Materi::factory()->create();

        $this->delete("/kelola-materi/{$materi->id}")->assertForbidden();

        $this->assertModelExists($materi);
    }

    // ------------------------------------------------------------------
    // Fungsi yang DIIZINKAN berjalan benar
    // ------------------------------------------------------------------

    public function test_admin_dapat_menambah_pengguna_dengan_role_pilihan(): void
    {
        $this->sebagai('admin');

        $this->post('/admin/pengguna', [
            'name' => 'Dosen Baru', 'email' => 'baru@uji.test', 'password' => 'password123', 'role' => 'dosen',
        ])->assertRedirect('/admin/pengguna');

        $this->assertDatabaseHas('users', ['email' => 'baru@uji.test', 'role' => 'dosen']);
    }

    public function test_admin_tidak_dapat_memakai_role_di_luar_daftar(): void
    {
        $this->sebagai('admin');

        $this->post('/admin/pengguna', [
            'name' => 'Aneh', 'email' => 'aneh@uji.test', 'password' => 'password123', 'role' => 'superadmin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'aneh@uji.test']);
    }

    public function test_admin_tidak_dapat_menghapus_atau_menurunkan_role_akun_sendiri(): void
    {
        $admin = $this->sebagai('admin');

        $this->delete("/admin/pengguna/{$admin->id}")->assertSessionHas('error');
        $this->assertModelExists($admin);

        $this->put("/admin/pengguna/{$admin->id}", [
            'name' => $admin->name, 'email' => $admin->email, 'role' => 'mahasiswa',
        ])->assertSessionHasErrors('role');
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_dosen_dapat_membuat_materi_dan_menginput_nilai(): void
    {
        $dosen = $this->sebagai('dosen');
        $mhs = User::factory()->mahasiswa()->create();

        $this->post('/kelola-materi', ['judul' => 'Materi Uji', 'mata_kuliah' => 'PW', 'isi' => 'Isi'])
            ->assertRedirect('/kelola-materi');
        $this->assertDatabaseHas('materi', ['judul' => 'Materi Uji', 'dibuat_oleh' => $dosen->id]);

        $this->post('/kelola-nilai', ['mahasiswa_id' => $mhs->id, 'mata_kuliah' => 'PW', 'nilai' => 90])
            ->assertRedirect('/kelola-nilai');
        $this->assertDatabaseHas('nilai', ['mahasiswa_id' => $mhs->id, 'nilai' => 90, 'dosen_id' => $dosen->id]);
    }

    public function test_nilai_hanya_boleh_diberikan_kepada_akun_mahasiswa(): void
    {
        $this->sebagai('dosen');
        $bukanMhs = User::factory()->admin()->create();

        $this->post('/kelola-nilai', ['mahasiswa_id' => $bukanMhs->id, 'mata_kuliah' => 'PW', 'nilai' => 90])
            ->assertSessionHasErrors('mahasiswa_id');
    }

    public function test_nilai_harus_antara_0_sampai_100(): void
    {
        $this->sebagai('dosen');
        $mhs = User::factory()->mahasiswa()->create();

        $this->post('/kelola-nilai', ['mahasiswa_id' => $mhs->id, 'mata_kuliah' => 'PW', 'nilai' => 101])
            ->assertSessionHasErrors('nilai');
    }

    // ------------------------------------------------------------------
    // Pertahanan berlapis: Middleware (route) + Policy (data)
    // ------------------------------------------------------------------

    public function test_mahasiswa_hanya_melihat_nilai_miliknya_sendiri(): void
    {
        $saya = $this->sebagai('mahasiswa');
        Nilai::factory()->create(['mahasiswa_id' => $saya->id, 'mata_kuliah' => 'Matkul Saya']);
        Nilai::factory()->create(['mata_kuliah' => 'Matkul Orang Lain']);

        $this->get('/nilai-saya')
            ->assertOk()
            ->assertSee('Matkul Saya')
            ->assertDontSee('Matkul Orang Lain');
    }

    public function test_mahasiswa_tidak_dapat_membuka_detail_nilai_milik_orang_lain(): void
    {
        $saya = $this->sebagai('mahasiswa');
        $milikSaya = Nilai::factory()->create(['mahasiswa_id' => $saya->id]);
        $milikLain = Nilai::factory()->create();

        $this->get("/nilai/{$milikSaya->id}")->assertOk();
        $this->get("/nilai/{$milikLain->id}")->assertForbidden();
    }

    public function test_dosen_dapat_membuka_detail_nilai_siapa_saja(): void
    {
        $this->sebagai('dosen');
        $nilai = Nilai::factory()->create();

        $this->get("/nilai/{$nilai->id}")->assertOk();
    }

    public function test_menu_disembunyikan_tetapi_url_tetap_dijaga_server(): void
    {
        $this->sebagai('mahasiswa');

        // Menu admin tidak tampil...
        $this->get('/dashboard')->assertOk()->assertDontSee('Manajemen Pengguna');
        // ...dan mengetik URL-nya langsung tetap ditolak oleh server.
        $this->get('/admin/pengguna')->assertForbidden();
    }
}
