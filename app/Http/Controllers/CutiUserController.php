<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CutiUserController extends Controller
{
    public function index()
    {
        $userSession = session('user');
        $namaKaryawan = $userSession['nama_karyawan'] ?? null;

        $riwayat_cuti = DB::table('cuti_karyawan')
            ->where('nama', $namaKaryawan)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung sisa jatah cuti tahun ini
        $tahunSekarang = date('Y');
        $totalCutiTerpakai = DB::table('cuti_karyawan')
            ->where('nama', $namaKaryawan)
            ->whereYear('tanggal_mulai', $tahunSekarang)
            ->where('status', 'Disetujui')
            ->where('jenis_cuti', 'Cuti Tahunan')
            ->sum('jumlah_hari');

        $sisaJatah = max(0, 12 - $totalCutiTerpakai);

        return view('user.cuti.cuti-personal', compact('riwayat_cuti', 'sisaJatah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'lampiran_dokumen' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $userSession = session('user');
        $namaKaryawan = $userSession['nama_karyawan'] ?? null;

        if (!$namaKaryawan) {
            return redirect()->back()->with('error', 'Sesi tidak ditemukan. Silakan login kembali.');
        }

        // 1. Hitung durasi pengajuan saat ini
        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $jumlah_hari_pengajuan = $mulai->diffInDays($selesai) + 1;

        // 2. Hitung total cuti yang sudah diambil (Tahun berjalan)
        $tahunSekarang = $mulai->year;
        $totalCutiLalu = DB::table('cuti_karyawan')
            ->where('nama', $namaKaryawan)
            ->whereYear('tanggal_mulai', $tahunSekarang)
            ->where('status', 'Disetujui')
            ->where('jenis_cuti', 'Cuti Tahunan')
            ->sum('jumlah_hari');

        $jatahTahunan = 12;
        $potonganGaji = 0;
        $biayaPerHari = 100000;

        // 3. Logika Potongan Gaji (Hanya jika memilih "Cuti Tahunan")
        if ($request->jenis_cuti == 'Cuti Tahunan') {
            $akumulasiCuti = $totalCutiLalu + $jumlah_hari_pengajuan;

            if ($akumulasiCuti > $jatahTahunan) {
                // Cari berapa hari yang melebihi batas
                $hariMelebihi = ($totalCutiLalu >= $jatahTahunan)
                    ? $jumlah_hari_pengajuan
                    : ($akumulasiCuti - $jatahTahunan);

                $potonganGaji = $hariMelebihi * $biayaPerHari;
            }
        }

        // Upload File
        $nama_file = null;
        if ($request->hasFile('lampiran_dokumen')) {
            $file = $request->file('lampiran_dokumen');
            $nama_file = time() . '_' . str_replace(' ', '_', $namaKaryawan) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cuti'), $nama_file);
        }

        try {
            DB::table('cuti_karyawan')->insert([
                'nama'             => $namaKaryawan,
                'jenis_cuti'       => $request->jenis_cuti,
                'tanggal_mulai'    => $request->tanggal_mulai,
                'tanggal_selesai'  => $request->tanggal_selesai,
                'jumlah_hari'      => $jumlah_hari_pengajuan,
                'keterangan'       => $request->keterangan . ($potonganGaji > 0 ? " (Potongan Gaji: Rp " . number_format($potonganGaji, 0, ',', '.') . ")" : ""),
                'lampiran_dokumen' => $nama_file,
                'status'           => 'Menunggu',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $pesan = $potonganGaji > 0
                ? "Berhasil! Pengajuan melebihi jatah, akan dipotong Rp " . number_format($potonganGaji, 0, ',', '.')
                : "Data berhasil disimpan!";

            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }
}
