# Akademik App — Pembatasan Hak Akses dengan Middleware (Laravel)

Tugas praktikum **Pemrograman Web — Middleware & RBAC**.
Aplikasi akademik sederhana dengan tiga role: **Admin**, **Dosen**, dan **Mahasiswa**.

Dibuat dengan **Laravel 12** (kode juga kompatibel dengan Laravel 11).

## Cara menjalankan

Prasyarat: PHP 8.2+, Composer, dan MySQL (atau SQLite, lihat catatan di bawah).

```bash
composer install
cp .env.example .env
php artisan key:generate

# 1. Buat database kosong bernama: akademik_app   (sesuai .env: DB_DATABASE)
# 2. Jalankan migrasi + isi akun uji
php artisan migrate --seed

php artisan serve
```

Buka <http://127.0.0.1:8000>.

> **Tidak mau pakai MySQL?** Pakai SQLite: di `.env` ubah `DB_CONNECTION=sqlite`, hapus/komentari baris
> `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, lalu `touch database/database.sqlite`
> (Windows: buat file kosong `database\database.sqlite`) dan jalankan `php artisan migrate --seed`.

## Akun uji

Semua akun memakai password `password`.

| Role      | Email                      |
|-----------|----------------------------|
| Admin     | `admin@akademik.test`      |
| Dosen     | `dosen@akademik.test`      |
| Mahasiswa | `mahasiswa@akademik.test`  |
| Mahasiswa | `mahasiswa2@akademik.test` |

## Hak akses (sesuai tabel studi kasus pada materi)

| Halaman / Fitur      | Admin | Dosen | Mahasiswa | Route                          | Middleware             |
|----------------------|:-----:|:-----:|:---------:|--------------------------------|------------------------|
| Dashboard            |  ✓    |  ✓    |  ✓        | `/dashboard`                   | `auth`                 |
| Manajemen pengguna   |  ✓    |  —    |  —        | `/admin`, `/admin/pengguna`    | `auth`, `role:admin`   |
| Kelola materi        |  ✓    |  ✓    |  —        | `/kelola-materi`               | `auth`, `role:admin,dosen` |
| Lihat materi         |  ✓    |  ✓    |  ✓        | `/materi`                      | `auth`                 |
| Kelola nilai         |  ✓    |  ✓    |  —        | `/kelola-nilai`                | `auth`, `role:admin,dosen` |
| Lihat nilai pribadi  |  ✓    |  —    |  ✓        | `/nilai-saya`                  | `auth`, `role:admin,mahasiswa` |
| Halaman Dosen        |  —    |  ✓    |  —        | `/dosen`                       | `auth`, `role:dosen`   |
| Halaman Mahasiswa    |  —    |  —    |  ✓        | `/mahasiswa`                   | `auth`, `role:mahasiswa` |

Daftar lengkap route: `php artisan route:list`.

## Pemetaan ke butir tugas

| Butir tugas | Lokasi |
|---|---|
| Login & logout | `app/Http/Controllers/AuthController.php`, `resources/views/auth/login.blade.php` |
| Tabel `users` dengan kolom `role` | `database/migrations/2026_11_17_000001_add_role_to_users_table.php` |
| Middleware `CheckRole` | `app/Http/Middleware/CheckRole.php` |
| Daftarkan alias `role` | `bootstrap/app.php` |
| Lindungi route pengguna, materi, nilai | `routes/web.php` |
| Uji minimal 3 akun | `tests/Feature/AksesRoleTest.php`, `docs/uji-akses.sh` |
| Dokumentasi hasil berhasil & ditolak | [`docs/PENGUJIAN.md`](docs/PENGUJIAN.md) + `docs/screenshots/` |

## Inti implementasi

**Middleware** (`app/Http/Middleware/CheckRole.php`)

```php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (! $request->user()) {
        return redirect()->route('login');          // belum login -> ke halaman login
    }

    if (! in_array($request->user()->role, $roles, true)) {
        abort(403, 'Anda tidak memiliki hak akses!'); // role tidak cocok -> 403
    }

    return $next($request);
}
```

**Alias** (`bootstrap/app.php`)

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias(['role' => CheckRole::class]);
})
```

**Route** (`routes/web.php`) — `auth` selalu dipasang sebelum `role`:

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () { ... });
Route::middleware(['auth', 'role:admin,dosen'])->group(function () { /* kelola-materi, kelola-nilai */ });
```

## Praktik keamanan yang diterapkan (slide 16)

- **Proteksi di sisi server**: menu disembunyikan per role, tetapi yang menjaga adalah middleware (ada test yang membuktikan URL tetap ditolak).
- **Autentikasi sebelum otorisasi**: `auth` → `role`.
- **Least Privilege**: Admin pun ditolak di `/dosen` dan `/mahasiswa`; Dosen ditolak di `/nilai-saya`.
- **Role tidak bisa dimanipulasi**: kolom `role` tidak ada di `$fillable`; tidak ada route registrasi publik; role hanya diubah Admin dengan validasi `Rule::in(User::ROLES)`.
- **Route tulis juga dilindungi** (POST/PUT/DELETE), bukan hanya halaman GET.
- **CSRF** aktif pada semua form; `session()->regenerate()` saat login; `throttle` pada login.
- **Policy untuk data** (`app/Policies/NilaiPolicy.php`): mahasiswa hanya dapat membuka detail nilai miliknya sendiri (`/nilai/{id}`) — Middleware mengamankan *jalur*, Policy mengamankan *data*.
- Admin tidak dapat menghapus / menurunkan role akunnya sendiri (cegah terkunci).

## Menjalankan pengujian

```bash
php artisan test              # 61 test otomatis (memakai SQLite in-memory, tidak menyentuh database Anda)

php artisan serve             # terminal 1
bash docs/uji-akses.sh        # terminal 2: uji HTTP nyata dengan curl untuk 3 akun + tamu
```
