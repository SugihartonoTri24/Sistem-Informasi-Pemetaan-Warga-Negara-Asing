<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foreigner extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'nomor_paspor',
        'warganegara',
        'jenis_izin_tinggal',
        'masa_berlaku_izin_tinggal',
        'penjamin',
        'jenis_penjamin',
        'alamat',
        'latitude',
        'longitude',
    ];
}
