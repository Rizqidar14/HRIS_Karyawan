<?php
// app/Http/Controllers/KlaimUserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KlaimUserController extends Controller
{
    // Halaman form klaim
    public function index()
    {
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;
        $divisi = $userSession['divisi'] ?? 'Umum';

        // Generate nomor klaim otomatis
        $tahun = date('Y');
        $bulan = date('m');
        $lastKlaim = DB::table('klaim')
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastKlaim) {
            $lastNumber = intval(substr($lastKlaim->nomor_klaim, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        $nomorKlaim = "KLM/{$tahun}{$bulan}/{$newNumber}";

        // Ambil riwayat klaim user
        $riwayatKlaim = DB::table('klaim')
            ->where('nama_karyawan', $userName)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.klaim.klaim-personal', compact('userSession', 'nomorKlaim', 'riwayatKlaim', 'userName', 'divisi'));
    }

    // Simpan klaim
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_klaim' => 'required|date',
            'kategori' => 'required',
            'deskripsi' => 'required|string|min:5',
            'nominal' => 'required|numeric|min:1000',
            'bukti' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;
        $divisi = $userSession['divisi'] ?? 'Umum';

        // Upload bukti
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bukti_klaim'), $fileName);
            $buktiPath = 'uploads/bukti_klaim/' . $fileName;
        }

        // Simpan ke database
        DB::table('klaim')->insert([
            'nomor_klaim' => $request->nomor_klaim,
            'nama_karyawan' => $userName,
            'divisi' => $divisi,
            'tanggal_klaim' => $request->tanggal_klaim,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'nominal' => $request->nominal,
            'bukti' => $buktiPath,
            'status' => 'pending',
            'keterangan' => $request->keterangan,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Klaim berhasil diajukan! Menunggu approval admin.');
    }

    // Riwayat klaim
    public function riwayat(Request $request)
    {
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;

        $query = DB::table('klaim')->where('nama_karyawan', $userName);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_klaim', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_klaim', $request->tahun);
        }

        $riwayat = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('user.klaim.riwayat-klaim', compact('riwayat', 'userSession'));
    }
}
