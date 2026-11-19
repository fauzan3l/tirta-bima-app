<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAtlet extends Model
{
    use HasFactory;

    protected $table = 'data_atlet';

    protected $fillable = [
        'user_id',
        'name',
        'kelompok',
        'nik',
        'nisnas',
        'ttl',
        'phone',
        'email',
        'tinggi_badan',
        'berat_badan',
        'sekolah',
        'kelas',
    ];

    protected function casts(): array
    {
        return [
            'tinggi_badan' => 'float',
            'berat_badan'  => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}