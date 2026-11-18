<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelolaMateriController;
use App\Http\Controllers\KelolaNilaiController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\NilaiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute publik / tamu (guest)
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Rute yang membutuhkan login  (middleware: auth)
| Semua role (admin, dosen, mahasiswa) boleh masuk.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lihat materi: semua role
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/{materi}', [MateriController::class, 'show'])->name('materi.show');

    // Detail nilai: semua role login, tetapi dibatasi lagi oleh NilaiPolicy (tingkat data)
    Route::get('/nilai/{nilai}', [NilaiController::class, 'show'])->name('nilai.show');
});

/*
|--------------------------------------------------------------------------
| Rute per role  (middleware: auth + role:...)
| 'auth' selalu dipasang SEBELUM 'role' (autentikasi dulu, baru otorisasi).
|--------------------------------------------------------------------------
*/

// ADMIN saja: halaman admin + manajemen pengguna
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'admin'])->name('index');
    Route::resource('pengguna', UserController::class)
        ->except('show')
        ->parameters(['pengguna' => 'user']);
});

// DOSEN saja
Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen', [DashboardController::class, 'dosen'])->name('dosen.index');
});

// MAHASISWA saja
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa', [DashboardController::class, 'mahasiswa'])->name('mahasiswa.index');
});

// ADMIN + DOSEN: kelola materi & kelola nilai
Route::middleware(['auth', 'role:admin,dosen'])->group(function () {
    Route::resource('kelola-materi', KelolaMateriController::class)
        ->except('show')
        ->parameters(['kelola-materi' => 'materi']);

    Route::resource('kelola-nilai', KelolaNilaiController::class)
        ->except('show')
        ->parameters(['kelola-nilai' => 'nilai']);
});

// ADMIN + MAHASISWA: lihat nilai pribadi
Route::middleware(['auth', 'role:admin,mahasiswa'])->group(function () {
    Route::get('/nilai-saya', [NilaiController::class, 'saya'])->name('nilai.saya');
});
