<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiApplication extends Model
{
    use HasFactory;

    /**
     * Nama tabel (opsional, karena Laravel akan otomatis mendeteksi dari nama model jamak)
     */
    protected $table = 'cuti_applications';

    /**
     * Kolom yang dapat diisi
     */
    protected $fillable = [
    'taruna_id',
    'alamat_cuti', // json
    'tujuan',
    'nama_kerabat',
    'nomor_kerabat',
    'transportasi',
    'tiket_path',
    'status',
    'approved_by_orangtua',
    'approved_at',
];

protected $casts = [
    'alamat_cuti' => 'array', // otomatis decode/encode JSON
    'approved_at' => 'datetime',
];

    // Relasi ke taruna (pengguna dengan role taruna)
    public function taruna()
    {
        return $this->belongsTo(User::class, 'taruna_id');
    }

    // Relasi ke orang tua yang menyetujui
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_orangtua');
    }
}
