<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wna extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'nomor_paspor',
        'kewarganegaraan',
        'jenis_izin_tinggal',
        'penjamin',
        'jenis_penjamin',
        'lokasi_kegiatan',
    ];
}