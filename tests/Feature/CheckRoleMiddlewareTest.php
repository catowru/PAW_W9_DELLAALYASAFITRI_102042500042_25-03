<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CheckRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_alias_role_terdaftar(): void
    {
        $alias = app(Kernel::class)->getMiddlewareAliases();

        $this->assertSame(CheckRole::class, $alias['role']);
    }

    public function test_tamu_dialihkan_ke_login(): void
    {
        $request = Request::create('/x');
        $request->setUserResolver(fn () => null);

        $response = (new CheckRole)->handle($request, fn () => response('lolos'), 'admin');

        $this->assertSame(route('login'), $response->headers->get('Location'));
    }

    public function test_role_yang_tidak_cocok_menghasilkan_403(): void
    {
        $request = Request::create('/x');
        $request->setUserResolver(fn () => User::factory()->mahasiswa()->make());

        try {
            (new CheckRole)->handle($request, fn () => response('lolos'), 'admin', 'dosen');
            $this->fail('Seharusnya melempar 403.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            $this->assertSame('Anda tidak memiliki hak akses!', $e->getMessage());
        }
    }

    public function test_salah_satu_dari_beberapa_role_diizinkan(): void
    {
        $request = Request::create('/x');
        $request->setUserResolver(fn () => User::factory()->dosen()->make());

        $response = (new CheckRole)->handle($request, fn () => response('lolos'), 'admin', 'dosen');

        $this->assertSame('lolos', $response->getContent());
    }
}
