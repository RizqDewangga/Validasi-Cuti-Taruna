<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Untuk taruna: tanggal lahir
            $table->date('tanggal_lahir')->nullable()->after('npm');
            // Untuk orang tua: id anak (taruna)
            $table->unsignedBigInteger('child_id')->nullable()->after('tanggal_lahir_anak');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lahir', 'child_id']);
        });
    }
};
