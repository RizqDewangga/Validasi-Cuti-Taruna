<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Register untuk taruna
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'npm' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'npm' => $request->npm,
            'password' => Hash::make($request->password),
            'role' => 'taruna',
        ]);

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 201);
    }

    // Login untuk taruna
    public function loginTaruna(Request $request)
    {
        $request->validate([
            'npm' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('npm', $request->npm)
                    ->where('role', 'taruna')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'npm' => ['NPM atau password salah.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }

    // Login untuk orang tua
    public function loginOrangTua(Request $request)
{
    $request->validate([
        'nama_ibu' => 'required|string',
        'nama_anak' => 'required|string',
        'tanggal_lahir_anak' => 'required|date',
    ]);

    // Cari user orang tua
    $orangTua = User::where('nama_lengkap', $request->nama_ibu)
                    ->where('role', 'orang_tua')
                    ->first();

    if (!$orangTua) {
        return response()->json([
            'status' => 'error',
            'message' => 'Data orang tua tidak ditemukan'
        ], 404);
    }

    // Debug: lihat nilai yang dibandingkan
    \Log::info('Input tanggal: ' . $request->tanggal_lahir_anak);
    \Log::info('DB tanggal: ' . $orangTua->tanggal_lahir_anak);

    // Bandingkan tanggal dengan cara yang lebih fleksibel
    $inputDate = date('Y-m-d', strtotime($request->tanggal_lahir_anak));
    $dbDate = date('Y-m-d', strtotime($orangTua->tanggal_lahir_anak));

    if ($inputDate !== $dbDate) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tanggal lahir anak tidak sesuai. Input: ' . $inputDate . ', DB: ' . $dbDate
        ], 401);
    }

    // Cari taruna
    $taruna = User::where('nama_lengkap', $request->nama_anak)
                  ->where('role', 'taruna')
                  ->first();

    if (!$taruna) {
        return response()->json([
            'status' => 'error',
            'message' => 'Data taruna tidak ditemukan'
        ], 404);
    }

    $token = $orangTua->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'data' => [
            'user' => $orangTua,
            'token' => $token,
            'anak' => $taruna
        ]
    ]);
}

    // Login untuk admin
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Gunakan password tetap (misal: "admin123") atau cek dari database
        // Cara sederhana: cari user dengan role admin dan password cocok
        // Tapi karena kita pakai hashed, kita harus cek dengan Hash::check
        $admin = User::where('role', 'admin')->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password admin salah'
            ], 401);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $admin,
                'token' => $token,
            ]
        ]);
    }

    // Logout (hapus token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }

    // Mendapatkan data user yang sedang login
    public function user(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }
}
