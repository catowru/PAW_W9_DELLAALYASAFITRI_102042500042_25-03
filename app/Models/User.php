<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_DOSEN = 'dosen';
    public const ROLE_MAHASISWA = 'mahasiswa';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_DOSEN,
        self::ROLE_MAHASISWA,
    ];

    /**
     * Atribut yang boleh di-mass-assign.
     *
     * 'role' SENGAJA tidak dimasukkan: role hanya boleh ditetapkan oleh sistem
     * (seeder) atau Admin lewat kode yang eksplisit, bukan dari input form bebas.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function roleLabel(): string
    {
        return ucfirst($this->role);
    }

    /** Nilai milik mahasiswa ini. */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class, 'mahasiswa_id');
    }
}
