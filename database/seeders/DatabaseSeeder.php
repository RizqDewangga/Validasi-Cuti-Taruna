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
    'nama_lengkap' => 'Budi Santoso',
    'npm' => '2021001',
    'password' => Hash::make('taruna123'),
    'role' => 'taruna',
    'nama_ibu' => 'Siti Aminah',
    'tanggal_lahir' => '2000-01-01',
]);

// Orang tua tidak perlu diisi manual, akan dibuat saat login pertama

        // Buat orang tua contoh
        User::create([
            'nama_lengkap' => 'Siti Aminah', // nama ibu
            'password' => Hash::make('ortu123'),
            'role' => 'orang_tua',
            'nama_ibu' => 'Siti Aminah', // diisi sama dengan nama_lengkap untuk memudahkan
            'tanggal_lahir_anak' => '2000-01-01', // contoh tanggal lahir anak (taruna)
        ]);

        // Bisa tambahkan data lain jika perlu
    }
}
