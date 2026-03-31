<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
     
        \App\Models\Jurusan::insert([
            ['nama_jurusan'=>'RPL'],
            ['nama_jurusan'=>'TKJ'],
            ['nama_jurusan'=>'Elektro'],
        ]);
        \App\Models\Kelas::factory()->count(9)->create();
        \App\Models\Student::factory()->count(30)->create();

    }
}
