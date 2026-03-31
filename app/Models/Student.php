<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Student extends Model
{
    use HasFactory;
    
    protected $table = 'student';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'nis',
        'nama',
        'jurusan_id',
        'kelas_id',
        'foto_profil',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class , 'jurusan_id', 'id_jurusan');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class , 'kelas_id', 'id_kelas');
    }
}
