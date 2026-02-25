<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat admin
        User::create([
            'nama_lengkap' => 'Administrator',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            // npm, nama_ibu, tanggal_lahir_anak tidak diisi karena untuk admin
        ]);

        // Taruna contoh
User::create([
    'nama_lengkap' => 'Jihan Safirah',
    'npm' => '20032005',
    'prodi' => 'S1 Teknik Industri',
    'password' => Hash::make('jihansafirah'),
    'role' => 'taruna',
    'nama_ibu' => 'mama jihan',
    'tanggal_lahir' => '2003-03-20',
]);

// Orang tua tidak perlu diisi manual, akan dibuat saat login pertama

        // Buat orang tua contoh
        User::create([
            'nama_lengkap' => 'mama jihan', // nama ibu
            'password' => Hash::make('mama123'),
            'role' => 'orang_tua',
            'nama_ibu' => 'mama jihan', // diisi sama dengan nama_lengkap untuk memudahkan
            'tanggal_lahir_anak' => '2003-03-20', // contoh tanggal lahir anak (taruna)
        ]);

        // Bisa tambahkan data lain jika perlu
    }
}
