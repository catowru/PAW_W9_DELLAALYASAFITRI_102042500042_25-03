<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = ['mahasiswa_id', 'dosen_id', 'mata_kuliah', 'nilai', 'keterangan'];

    protected function casts(): array
    {
        return ['nilai' => 'integer'];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    /** Nilai huruf: $nilai->huruf */
    protected function huruf(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->nilai >= 80 => 'A',
            $this->nilai >= 70 => 'B',
            $this->nilai >= 60 => 'C',
            $this->nilai >= 50 => 'D',
            default => 'E',
        });
    }
}
