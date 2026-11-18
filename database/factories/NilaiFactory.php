<?php

namespace Database\Factories;

use App\Models\Nilai;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nilai>
 */
class NilaiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mahasiswa_id' => User::factory()->mahasiswa(),
            'dosen_id' => User::factory()->dosen(),
            'mata_kuliah' => 'Pemrograman Web',
            'nilai' => fake()->numberBetween(40, 100),
            'keterangan' => null,
        ];
    }
}
