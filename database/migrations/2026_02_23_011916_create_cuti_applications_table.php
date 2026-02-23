<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('cuti_applications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('taruna_id')->constrained('users')->onDelete('cascade');
        $table->string('alamat_tujuan');
        $table->string('alamat_dituju');
        $table->string('transportasi');
        $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
        $table->foreignId('approved_by_orangtua')->nullable()->constrained('users');
        $table->timestamp('approved_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti_applications');
    }
};
