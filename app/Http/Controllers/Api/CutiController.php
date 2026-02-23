<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CutiApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CutiController extends Controller
{
    /**
     * Menampilkan daftar cuti milik taruna yang login (untuk role taruna)
     * Atau untuk orang tua, menampilkan cuti anaknya (perlu parameter anak_id)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role == 'taruna') {
            // Taruna melihat cuti miliknya sendiri
            $cuti = CutiApplication::where('taruna_id', $user->id)
                        ->with('taruna')
                        ->latest()
                        ->get();
        } elseif ($user->role == 'orang_tua') {
            // Orang tua: butuh taruna_id dari request (misal dikirim via query ?taruna_id=...)
            // Karena kita tidak tahu anaknya siapa, kita asumsikan dikirim dari frontend
            $request->validate([
                'taruna_id' => 'required|exists:users,id'
            ]);
            $cuti = CutiApplication::where('taruna_id', $request->taruna_id)
                        ->with('taruna', 'approver')
                        ->latest()
                        ->get();
        } else {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $cuti
        ]);
    }

    /**
     * Menyimpan pengajuan cuti baru (hanya untuk taruna)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role != 'taruna') {
            return response()->json(['status' => 'error', 'message' => 'Hanya taruna yang bisa mengajukan cuti'], 403);
        }

        $validator = Validator::make($request->all(), [
            'alamat_tujuan' => 'required|string',
            'alamat_dituju' => 'required|string',
            'transportasi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $cuti = CutiApplication::create([
            'taruna_id' => $user->id,
            'alamat_tujuan' => $request->alamat_tujuan,
            'alamat_dituju' => $request->alamat_dituju,
            'transportasi' => $request->transportasi,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $cuti
        ], 201);
    }

    /**
     * Menampilkan detail satu cuti
     */
    public function show(Request $request, $id)
    {
        $cuti = CutiApplication::with('taruna', 'approver')->findOrFail($id);

        // Cek otorisasi: hanya pemilik (taruna) atau orang tua yang berhak
        $user = $request->user();
        if ($user->role == 'taruna' && $cuti->taruna_id != $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak melihat cuti ini'], 403);
        }
        // Untuk orang tua, kita perlu cek apakah cuti ini milik anaknya. Sederhananya, kita cek dari taruna_id yang dikirim di query? Atau kita bisa cek relasi orang tua-taruna. Untuk sementara, lewati.

        return response()->json([
            'status' => 'success',
            'data' => $cuti
        ]);
    }

    /**
     * Mengupdate cuti (hanya jika status masih pending dan user adalah pemilik)
     */
    public function update(Request $request, $id)
    {
        $cuti = CutiApplication::findOrFail($id);
        $user = $request->user();

        if ($user->role != 'taruna' || $cuti->taruna_id != $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if ($cuti->status != 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Cuti sudah diproses, tidak bisa diubah'], 400);
        }

        $validator = Validator::make($request->all(), [
            'alamat_tujuan' => 'sometimes|string',
            'alamat_dituju' => 'sometimes|string',
            'transportasi' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $cuti->update($request->only('alamat_tujuan', 'alamat_dituju', 'transportasi'));

        return response()->json([
            'status' => 'success',
            'data' => $cuti
        ]);
    }

    /**
     * Menghapus cuti (hanya jika pending)
     */
    public function destroy(Request $request, $id)
    {
        $cuti = CutiApplication::findOrFail($id);
        $user = $request->user();

        if ($user->role != 'taruna' || $cuti->taruna_id != $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if ($cuti->status != 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Cuti sudah diproses, tidak bisa dihapus'], 400);
        }

        $cuti->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cuti berhasil dihapus'
        ]);
    }

    /**
     * Menyetujui cuti (oleh orang tua)
     */
    public function approve(Request $request, $id)
    {
        $cuti = CutiApplication::findOrFail($id);
        $user = $request->user();

        if ($user->role != 'orang_tua') {
            return response()->json(['status' => 'error', 'message' => 'Hanya orang tua yang dapat menyetujui'], 403);
        }

        // Validasi apakah cuti ini milik anak orang tua tersebut
        // Kita perlu taruna_id yang dikirim dari client
        $request->validate([
            'taruna_id' => 'required|exists:users,id'
        ]);

        if ($cuti->taruna_id != $request->taruna_id) {
            return response()->json(['status' => 'error', 'message' => 'Cuti bukan milik anak Anda'], 403);
        }

        // Cek apakah status masih pending
        if ($cuti->status != 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Cuti sudah diproses sebelumnya'], 400);
        }

        $cuti->status = 'disetujui';
        $cuti->approved_by_orangtua = $user->id;
        $cuti->approved_at = now();
        $cuti->save();

        return response()->json([
            'status' => 'success',
            'data' => $cuti
        ]);
    }

    /**
     * Menolak cuti (oleh orang tua)
     */
    public function reject(Request $request, $id)
    {
        $cuti = CutiApplication::findOrFail($id);
        $user = $request->user();

        if ($user->role != 'orang_tua') {
            return response()->json(['status' => 'error', 'message' => 'Hanya orang tua yang dapat menolak'], 403);
        }

        $request->validate([
            'taruna_id' => 'required|exists:users,id'
        ]);

        if ($cuti->taruna_id != $request->taruna_id) {
            return response()->json(['status' => 'error', 'message' => 'Cuti bukan milik anak Anda'], 403);
        }

        if ($cuti->status != 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Cuti sudah diproses sebelumnya'], 400);
        }

        $cuti->status = 'ditolak';
        $cuti->approved_by_orangtua = $user->id;
        $cuti->approved_at = now();
        $cuti->save();

        return response()->json([
            'status' => 'success',
            'data' => $cuti
        ]);
    }
}
