<?php
// app/Http/Controllers/KlaimController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KlaimController extends Controller
{
    // ==================== USER SECTION ====================

    // Halaman form pengajuan klaim
    public function index()
    {
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;
        $divisi = $userSession['divisi'] ?? 'Umum';

        if (!$userName) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Generate nomor klaim otomatis
        $tahun = date('Y');
        $bulan = date('m');
        $lastKlaim = DB::table('klaim')
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastKlaim && $lastKlaim->nomor_klaim) {
            $lastNumber = intval(substr($lastKlaim->nomor_klaim, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        $nomorKlaim = "KLM/{$tahun}{$bulan}/{$newNumber}";

        return view('user.klaim.klaim', compact('userName', 'divisi', 'nomorKlaim'));
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

    // Riwayat klaim user
    public function riwayat(Request $request)
    {
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;

        if (!$userName) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $query = DB::table('klaim')->where('nama_karyawan', $userName);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riwayat = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('user.klaim.riwayat', compact('riwayat'));
    }

    // ==================== ADMIN SECTION ====================

    // Halaman utama admin (menampilkan klaim pending)
    public function adminIndex(Request $request)
    {
        $query = DB::table('klaim')->where('status', 'pending');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_klaim', 'LIKE', "%{$search}%")
                    ->orWhere('nama_karyawan', 'LIKE', "%{$search}%");
            });
        }

        $klaim = $query->orderBy('created_at', 'desc')->paginate(20);

        $statistik = [
            'pending' => DB::table('klaim')->where('status', 'pending')->count(),
            'approved' => DB::table('klaim')->where('status', 'approved')->count(),
            'rejected' => DB::table('klaim')->where('status', 'rejected')->count(),
            'paid' => DB::table('klaim')->where('status', 'paid')->count(),
        ];

        // SINKRON: Mengarah ke admin/klaim/klaim-karyawan.blade.php
        return view('admin.klaim.klaim-karyawan', compact('klaim', 'statistik'));
    }

    public function adminRiwayat(Request $request)
    {
        $query = DB::table('klaim');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_klaim', 'LIKE', "%{$search}%")
                    ->orWhere('nama_karyawan', 'LIKE', "%{$search}%");
            });
        }

        $riwayat = $query->orderBy('created_at', 'desc')->paginate(20);

        // SINKRON: Mengarah ke admin/klaim/riwayat-klim.blade.php
        return view('admin.klaim.riwayat-klim', compact('riwayat'));
    }

    public function approve(Request $request, $id)
    {
        DB::table('klaim')->where('id', $id)->update([
            'status' => 'approved',
            'catatan_admin' => $request->catatan,
            'approved_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        return redirect()->back()->with('success', 'Klaim berhasil disetujui!');
    }

    public function reject(Request $request, $id)
    {
        DB::table('klaim')->where('id', $id)->update([
            'status' => 'rejected',
            'catatan_admin' => $request->alasan,
            'updated_at' => Carbon::now()
        ]);
        return redirect()->back()->with('success', 'Klaim ditolak.');
    }
}
