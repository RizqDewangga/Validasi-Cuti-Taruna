<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CutiApplication;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Menampilkan semua pengajuan cuti (untuk admin)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role != 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $cuti = CutiApplication::with('taruna', 'approver')
                    ->latest()
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $cuti
        ]);
    }

    /**
     * (Opsional) Statistik untuk admin
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        if ($user->role != 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $total = CutiApplication::count();
        $pending = CutiApplication::where('status', 'pending')->count();
        $disetujui = CutiApplication::where('status', 'disetujui')->count();
        $ditolak = CutiApplication::where('status', 'ditolak')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'pending' => $pending,
                'disetujui' => $disetujui,
                'ditolak' => $ditolak,
            ]
        ]);
    }
}
