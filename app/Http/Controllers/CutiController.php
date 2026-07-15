<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CutiController extends Controller
{
    // Menampilkan daftar pengajuan cuti untuk Admin
    public function index()
    {
        // Mengambil semua data dari tabel cuti_karyawan sesuai database Anda
        $dataCuti = DB::table('cuti_karyawan')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.cuti.cuti-karyawan', compact('dataCuti'));
    }

    // Fungsi untuk Update Status (ACC atau Tolak)
    public function updateStatus(Request $request, $id)
    {
        // Validasi input status
        $status = $request->status; // 'Disetujui' atau 'Ditolak'

        DB::table('cuti_karyawan')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);

        return back()->with('success', "Status cuti berhasil diubah menjadi $status!");
    }
}
