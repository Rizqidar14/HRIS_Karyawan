<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('user')) {
            return redirect()->route('login');
        }

        $role = session('user')['role'];

        if ($role === 'admin') {
            return $this->adminDashboard();
        } else if ($role === 'karyawan') {
            return redirect()->route('user.dashboard');
        } else {
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }
    }

    public function adminDashboard()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');

            // 1. STATISTIK UTAMA
            $totalKaryawan = DB::table('karyawan')->count();
            $karyawanAktif = DB::table('karyawan')->where('status', 'Aktif')->count();
            $totalAkun = DB::table('users')->count();

            // 2. KEHADIRAN HARI INI
            $hadirHariIni = DB::table('absen')
                ->whereDate('tanggal_masuk', $today)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->count();

            $terlambatHariIni = DB::table('absen')
                ->whereDate('tanggal_masuk', $today)
                ->where('status', 'terlambat')
                ->count();

            // 3. DATA CUTI (PENTING: Pastikan nama tabel benar)
            $pengajuanCutiPending = DB::table('cuti_karyawan')
                ->where('status', 'Menunggu')
                ->count();

            $cutiTerbaru = DB::table('cuti_karyawan')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // 4. AKTIVITAS TERBARU (UPDATED: Sekarang dikunci khusus per hari ini saja)
            $aktivitasTerbaru = DB::table('absen')
                ->leftJoin('karyawan', 'absen.nama', '=', 'karyawan.nama')
                ->whereDate('absen.tanggal_masuk', $today) // <--- Mengunci data hanya tanggal hari ini
                ->select('absen.*', 'karyawan.divisi')
                ->orderBy('absen.jam_masuk', 'desc') // Cukup urutkan berdasarkan jam masuk terbaru
                ->limit(8)
                ->get();

            // 5. STATISTIK RINGKAS
            $statistikKehadiran = [
                'hadir' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'hadir')->count(),
                'terlambat' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'terlambat')->count(),
                'izin' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'izin')->count(),
                'sakit' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'sakit')->count(),
                'alpha' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'alpha')->count(),
            ];

            $totalAbsenHariIni = DB::table('absen')->whereDate('tanggal_masuk', $today)->count();
            $belumAbsen = max(0, $totalKaryawan - $totalAbsenHariIni);

            return view('admin.dashboard', [
                'totalKaryawan' => $totalKaryawan,
                'karyawanAktif' => $karyawanAktif,
                'totalAkun' => $totalAkun,
                'hadirHariIni' => $hadirHariIni,
                'terlambatHariIni' => $terlambatHariIni,
                'pengajuanCutiPending' => $pengajuanCutiPending,
                'cutiTerbaru' => $cutiTerbaru,
                'aktivitasTerbaru' => $aktivitasTerbaru,
                'statistikKehadiran' => $statistikKehadiran,
                'belumAbsen' => $belumAbsen,
                'today' => $today
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage());
            return view('admin.dashboard', ['error' => $e->getMessage()]);
        }
    }
}
