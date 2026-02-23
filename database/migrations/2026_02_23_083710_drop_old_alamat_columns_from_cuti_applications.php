<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuti_applications', function (Blueprint $table) {
            // Hapus kolom lama yang tidak digunakan
            $table->dropColumn(['alamat_tujuan', 'alamat_dituju']);
        });
    }

    public function down(): void
    {
        Schema::table('cuti_applications', function (Blueprint $table) {
            // Jika rollback, tambahkan kembali kolom
            $table->string('alamat_tujuan')->nullable();
            $table->string('alamat_dituju')->nullable();
        });
    }
};
