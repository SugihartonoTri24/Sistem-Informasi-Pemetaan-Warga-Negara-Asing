<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wnas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nomor_paspor')->unique();
            $table->string('kewarganegaraan');
            $table->string('jenis_izin_tinggal'); // Contoh: ITAS INVESTOR, ITAS KERJA
            $table->string('penjamin'); // Nama Penjamin / Perusahaan
            $table->enum('jenis_penjamin', ['PERUSAHAAN', 'PERORANGAN']);
            $table->string('lokasi_kegiatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wnas');
    }
};