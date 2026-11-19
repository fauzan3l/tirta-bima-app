<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramLatihan extends Model
{
    use HasFactory;

    protected $table = 'program_latihan';

    protected $fillable = [
        'kelompok',
        'fase',
        'judul_minggu',
        'target_event',
        'alat',
        'tanggal',
        'file_path',
        'file_name',
    ];
}