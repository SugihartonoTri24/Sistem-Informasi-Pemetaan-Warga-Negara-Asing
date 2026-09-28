<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foreigners', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nomor_paspor')->unique();
            $table->string('warganegara');
            $table->string('jenis_izin_tinggal'); // Contoh: ITAS INVESTOR, ITAP KELUARGA
            $table->date('masa_berlaku_izin_tinggal');
            $table->string('penjamin');
            $table->enum('jenis_penjamin', ['PERUSAHAAN', 'PERORANGAN'])->default('PERUSAHAAN');
            $table->text('alamat');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foreigners');
    }
}
