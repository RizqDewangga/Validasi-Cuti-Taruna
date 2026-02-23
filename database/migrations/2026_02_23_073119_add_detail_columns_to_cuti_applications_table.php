<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cuti_applications', function (Blueprint $table) {
            // Alamat cuti lengkap disimpan sebagai JSON
            $table->json('alamat_cuti')->nullable()->after('alamat_dituju');
            // Tujuan: orang_tua atau kerabat
            $table->enum('tujuan', ['orang_tua', 'kerabat'])->default('orang_tua')->after('alamat_cuti');
            // Jika kerabat, simpan nama dan nomor
            $table->string('nama_kerabat')->nullable()->after('tujuan');
            $table->string('nomor_kerabat')->nullable()->after('nama_kerabat');
            // Transportasi (string)
            $table->string('transportasi')->nullable()->change(); // ubah agar nullable? Tapi kita tetap wajibkan di form
            // Path tiket
            $table->string('tiket_path')->nullable()->after('transportasi');
        });
    }

    public function down()
    {
        Schema::table('cuti_applications', function (Blueprint $table) {
            $table->dropColumn(['alamat_cuti', 'tujuan', 'nama_kerabat', 'nomor_kerabat', 'tiket_path']);
            // Kembalikan transportasi ke tidak nullable jika sebelumnya not null
            $table->string('transportasi')->nullable(false)->change();
        });
    }
};
