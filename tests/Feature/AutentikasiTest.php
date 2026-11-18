<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutentikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_dibuka_tamu(): void
    {
        $this->get('/login')->assertOk()->assertSee('Login');
    }

    public function test_login_berhasil_diarahkan_ke_dashboard(): void
    {
        $user = User::factory()->dosen()->create(['email' => 'dosen@uji.test']);

        $this->post('/login', ['email' => 'dosen@uji.test', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_jika_password_salah(): void
    {
        User::factory()->create(['email' => 'a@uji.test']);

        $this->from('/login')
            ->post('/login', ['email' => 'a@uji.test', 'password' => 'salah'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_pengguna_yang_sudah_login_tidak_perlu_membuka_halaman_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/login')
            ->assertRedirect(); // diarahkan keluar dari /login
    }

    public function test_tabel_users_memiliki_kolom_role_dengan_default_mahasiswa(): void
    {
        $user = new User(['name' => 'X', 'email' => 'x@uji.test', 'password' => 'password']);
        $user->save();

        $this->assertSame('mahasiswa', $user->fresh()->role);
    }

    public function test_role_tidak_dapat_diisi_lewat_mass_assignment(): void
    {
        $user = User::create([
            'name' => 'Nakal', 'email' => 'nakal@uji.test', 'password' => 'password',
            'role' => 'admin', // upaya eskalasi hak akses
        ]);

        $this->assertSame('mahasiswa', $user->fresh()->role);
    }
}
