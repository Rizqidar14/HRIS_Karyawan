<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Ambil data user dari session
        $userSession = session('user');

        // Jika tidak ada session, redirect ke login
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Gunakan nama variabel yang konsisten
        $userName = $userSession['nama_karyawan'] ?? $userSession['username'] ?? 'User';

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $today = Carbon::today()->format('Y-m-d');

        // 1. Hitung kehadiran bulan ini
        $kehadiranBulanIni = DB::table('absen')
            ->where('nama', $userName)
            ->whereYear('tanggal_masuk', $currentYear)
            ->whereMonth('tanggal_masuk', $currentMonth)
            ->where('status', 'hadir')
            ->count();

        // Hitung total hari kerja di bulan ini (Senin-Jumat, exclude weekend)
        $totalHariKerja = $this->getTotalWorkDays($currentYear, $currentMonth);

        // Persentase kehadiran
        $persentaseKehadiran = $totalHariKerja > 0 ? round(($kehadiranBulanIni / $totalHariKerja) * 100) : 0;

        // 2. Hitung absensi sakit bulan ini
        $sakitBulanIni = DB::table('absen')
            ->where('nama', $userName)
            ->whereYear('tanggal_masuk', $currentYear)
            ->whereMonth('tanggal_masuk', $currentMonth)
            ->where('status', 'sakit')
            ->count();

        // 3. Hitung absensi izin bulan ini
        $izinBulanIni = DB::table('absen')
            ->where('nama', $userName)
            ->whereYear('tanggal_masuk', $currentYear)
            ->whereMonth('tanggal_masuk', $currentMonth)
            ->where('status', 'izin')
            ->count();

        // 4. HITUNG CUTI BULAN INI (Hanya mengambil data yang disetujui/acc oleh admin)
        // Catatan: Jika nama field tanggal/status di tabel cuti_karyawan Anda berbeda, sesuaikan di bawah ini
        $cutiBulanIni = DB::table('cuti_karyawan')
            ->where('nama', $userName)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where(function ($query) {
                $query->where('status', 'Disetujui')
                    ->orWhere('status', 'acc');
            })
            ->count();

        // 5. Cek absensi hari ini
        $absenHariIni = DB::table('absen')
            ->where('nama', $userName)
            ->whereDate('tanggal_masuk', $today)
            ->first();

        $statusHariIni = $absenHariIni ? $absenHariIni->status : 'Belum Absen';
        $jamMasuk = $absenHariIni ? $absenHariIni->jam_masuk : null;
        $jamKeluar = $absenHariIni ? $absenHariIni->jam_keluar : null;

        // Kirim semua variabel ke view termasuk 'cutiBulanIni'
        return view('user.dashboard-user', [
            'userName' => $userName,
            'kehadiranBulanIni' => $kehadiranBulanIni,
            'totalHariKerja' => $totalHariKerja,
            'persentaseKehadiran' => $persentaseKehadiran,
            'sakitBulanIni' => $sakitBulanIni,
            'izinBulanIni' => $izinBulanIni,
            'cutiBulanIni' => $cutiBulanIni, // Variabel baru dikirim ke view
            'statusHariIni' => $statusHariIni,
            'jamMasuk' => $jamMasuk,
            'jamKeluar' => $jamKeluar,
            'userSession' => $userSession
        ]);
    }

    /**
     * Hitung total hari kerja dalam sebulan (Senin - Jumat)
     */
    private function getTotalWorkDays($year, $month)
    {
        $date = Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $workDays = 0;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = Carbon::create($year, $month, $i);
            // Carbon: 1 = Senin, 2 = Selasa, 3 = Rabu, 4 = Kamis, 5 = Jumat, 6 = Sabtu, 7 = Minggu
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Senin sampai Jumat
                $workDays++;
            }
        }

        return $workDays;
    }
}
