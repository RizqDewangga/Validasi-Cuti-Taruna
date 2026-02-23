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
        'alamat_tujuan',
        'alamat_dituju',
        'transportasi',
        'status',
        'approved_by_orangtua',
        'approved_at',
    ];

    /**
     * Casting atribut
     */
    protected $casts = [
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
