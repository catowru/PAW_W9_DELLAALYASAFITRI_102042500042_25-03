<?php

namespace Database\Factories;

use App\Models\Materi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Materi>
 */
class MateriFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(4),
            'mata_kuliah' => 'Pemrograman Web',
            'isi' => fake()->paragraph(),
            'dibuat_oleh' => User::factory()->dosen(),
        ];
    }
}
