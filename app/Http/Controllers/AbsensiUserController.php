<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiUserController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;

        if (!$userName) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $absenHariIni = DB::table('absen')
            ->where('nama', $userName)
            ->whereDate('tanggal_masuk', $today)
            ->first();

        // Ambil nilai bulan dan tahun dari request (untuk filter)
        $bulan = $request->get('bulan', date('n')); // default bulan sekarang
        $tahun = $request->get('tahun', date('Y')); // default tahun sekarang

        // Daftar bulan (1-12)
        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Daftar tahun (5 tahun terakhir hingga tahun depan)
        $tahunSekarang = date('Y');
        $daftarTahun = [];
        for ($i = $tahunSekarang - 2; $i <= $tahunSekarang + 2; $i++) {
            $daftarTahun[] = $i;
        }

        // Riwayat dengan filter bulan dan tahun
        $riwayat_user = DB::table('absen')
            ->where('nama', $userName)
            ->whereMonth('tanggal_masuk', $bulan)
            ->whereYear('tanggal_masuk', $tahun)
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        return view('user.absensi.absensi-karyawan', compact(
            'absenHariIni',
            'riwayat_user',
            'userSession',
            'daftarBulan',
            'daftarTahun',
            'bulan',
            'tahun'
        ));
    }

    public function riwayat(Request $request)
    {
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;

        if (!$userName) return redirect()->route('login');

        // Query ke tabel absen
        $query = DB::table('absen')->where('nama', $userName);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_masuk', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_masuk', $request->tahun);
        }

        $riwayat = $query->orderBy('tanggal_masuk', 'desc')->paginate(15);

        $list_tahun = DB::table('absen')
            ->where('nama', $userName)
            ->selectRaw('YEAR(tanggal_masuk) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('user.absensi.riwayat-personal', compact('riwayat', 'list_tahun', 'userSession'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin',
            'latitude' => 'required_if:status,hadir',
            'longitude' => 'required_if:status,hadir',
            'keterangan_status' => 'required_if:status,sakit,izin'
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;

        if (!$userName) return redirect()->route('login');

        $absen = DB::table('absen')
            ->where('nama', $userName)
            ->whereDate('tanggal_masuk', $today)
            ->first();

        // Logika untuk Check-out
        if ($absen && $absen->status == 'hadir') {
            if ($absen->jam_keluar) {
                return redirect()->back()->with('error', 'Anda sudah Check-out hari ini.');
            }

            // Hitung durasi kerja normal
            $jamMasuk = Carbon::parse($absen->jam_masuk);
            $jamKeluar = Carbon::now();
            $diff = $jamMasuk->diff($jamKeluar);
            $totalJamText = $diff->format('%h jam %i menit');

            DB::table('absen')->where('id', $absen->id)->update([
                'jam_keluar' => $jamKeluar->format('H:i:s'),
                'total_jam' => $totalJamText,
                'lokasi_keluar' => ($request->latitude && $request->longitude) ? $request->latitude . ',' . $request->longitude : null
            ]);

            return redirect()->back()->with('success', 'Berhasil Check-out! Durasi: ' . $totalJamText);
        }

        // Logika Check-in baru
        if (!$absen) {
            DB::table('absen')->insert([
                'nama' => $userName,
                'tanggal_masuk' => $today,
                'jam_masuk' => Carbon::now()->format('H:i:s'),
                'status' => $request->status,
                'keterangan_status' => $request->keterangan_status,
                'lokasi_masuk' => ($request->latitude && $request->longitude) ? $request->latitude . ',' . $request->longitude : null
            ]);
            return redirect()->back()->with('success', 'Berhasil melakukan presensi!');
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan!');
    }

    public function storeLembur(Request $request)
    {
        $request->validate(['keterangan' => 'required|string|max:255']);

        $today = Carbon::today()->format('Y-m-d');
        $userSession = session('user');
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? null;

        $absen = DB::table('absen')
            ->where('nama', $userName)
            ->whereDate('tanggal_masuk', $today)
            ->first();

        if (!$absen || !$absen->jam_keluar) {
            return redirect()->back()->with('error', 'Silakan Check-out terlebih dahulu.');
        }

        // Hitung durasi lembur (dari jam keluar sampai sekarang)
        $jamCheckOut = Carbon::parse($absen->jam_keluar);
        $waktuInput = Carbon::now();
        $selisihMenit = $jamCheckOut->diffInMinutes($waktuInput);

        // Batasi maksimal 2 jam (120 menit)
        if ($selisihMenit > 120) $selisihMenit = 120;

        $jamLembur = floor($selisihMenit / 60);
        $menitLembur = $selisihMenit % 60;
        $durasiLembur = ($jamLembur > 0 ? $jamLembur . " jam " : "") . $menitLembur . " menit";

        DB::table('absen')->where('id', $absen->id)->update([
            'lembur' => $request->keterangan . " (Durasi: " . $durasiLembur . ")"
        ]);

        return redirect()->back()->with('success', 'Lembur tersimpan: ' . $durasiLembur);
    }
}
