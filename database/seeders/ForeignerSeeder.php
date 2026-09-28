<?php

namespace Database\Seeders;

use App\Models\Foreigner;
use Illuminate\Database\Seeder;

class ForeignerSeeder extends Seeder
{
    public function run(): void
    {
        Foreigner::create([
            'nama' => 'YANG XIUQING',
            'jenis_kelamin' => 'L',
            'nomor_paspor' => 'EN0816924',
            'warganegara' => 'CHINA',
            'jenis_izin_tinggal' => 'ITAS INVESTOR',
            'masa_berlaku_izin_tinggal' => '2027-08-06',
            'penjamin' => 'PT CHIN FOOD AND BEVERAGES',
            'jenis_penjamin' => 'PERUSAHAAN',
            'alamat' => 'JL.GREEN BABEL, RT.07/RW.02 DUSUN KAYU ARA, DESA JERUK, KECAMATAN PANGKALAN BARU, KAB BANGKA TENGAH',
            'latitude' => -2.1539898089723435,
            'longitude' => 106.13087929635869,
        ]);
    }
}
