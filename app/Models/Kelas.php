<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'tingkat',
        'jurusan_id',
        'golongan',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class , 'jurusan_id', 'id_jurusan');
    }

    public function students()
    {
        return $this->hasMany(Student::class , 'kelas_id', 'id_kelas');
    }

    /**
     * Nama kelas yang ditampilkan, misal: "10 RPL 1"
     */
    public function getNamaKelasAttribute(): string
    {
        $jurusanNama = $this->jurusan ? $this->jurusan->nama_jurusan : '?';
        return $this->tingkat . ' ' . $jurusanNama . ' ' . $this->golongan;
    }
}
