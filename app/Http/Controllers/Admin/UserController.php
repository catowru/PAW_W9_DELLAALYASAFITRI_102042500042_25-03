<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Manajemen pengguna (hanya Admin). Dilindungi di routes/web.php dengan role:admin.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.pengguna.index', [
            'users' => User::orderBy('role')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pengguna.form', ['user' => new User(['role' => User::ROLE_MAHASISWA])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $user->role = $data['role']; // role ditetapkan eksplisit oleh Admin, hanya nilai yang valid
        $user->save();

        return redirect()->route('admin.pengguna.index')->with('status', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        return view('admin.pengguna.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        // Cegah Admin mengunci dirinya sendiri dengan menurunkan role-nya.
        if ($user->is($request->user()) && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withInput()->withErrors(['role' => 'Anda tidak dapat mengubah role akun Anda sendiri.']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->role = $data['role'];
        $user->save();

        return redirect()->route('admin.pengguna.index')->with('status', "Pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.pengguna.index')->with('status', 'Pengguna berhasil dihapus.');
    }
}
