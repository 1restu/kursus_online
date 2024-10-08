<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MateriModel;
use App\Models\KtgMateriModel;

class KursusModel extends Model
{
    use HasFactory;

    protected $table = 'kursus';

    protected $fillable = [
        'nama_krs',
        'gambar',
        'original_gambar',
        'deskripsi',
        'biaya_krs',
        'durasi',
        'jam'
    ];

    public function materi()
    {
        return $this->hasMany(MateriModel::class, 'kursus_id');
    }

    public function pdkursus()
    {
        return $this->hasMany(PdKursusModel::class, 'id_krs');
    }

    public function pendaftar()
    {
        return $this->hasMany(PdKursusModel::class, 'id_krs', 'id');
    }

}
