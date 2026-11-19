<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BestTime extends Model
{
    use HasFactory;

    protected $table = 'best_times';

    protected $fillable = [
        'nama_atlet',
        'gaya',
        'jarak',
        'waktu',
        'tanggal',
        'keterangan',
    ];
}