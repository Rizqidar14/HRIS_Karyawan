<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenggajianKaryawanController extends Controller
{
    private function getTerRate($bruto)
    {
        if ($bruto <= 5400000) return 0;
        if ($bruto <= 5650000) return 0.0025;
        if ($bruto <= 6200000) return 0.005;
        if ($bruto <= 6950000) return 0.0075;
        if ($bruto <= 7100000) return 0.01;
        if ($bruto <= 7390000) return 0.0125;
        if ($bruto <= 7500000) return 0.015;
        if ($bruto <= 8800000) return 0.0175;
        if ($bruto <= 9650000) return 0.02;
        if ($bruto <= 10410000) return 0.0225;
        return 0.025;
    }

    private function hitungJamLembur($nama, $bulan, $tahun)
    {
        $absensi = DB::table('absen')
            ->where('nama', $nama)
            ->where('status', 'hadir')
            ->whereMonth('tanggal_masuk', $bulan)
            ->whereYear('tanggal_masuk', $tahun)
            ->whereNotNull('jam_masuk')
            ->whereNotNull('jam_keluar')
            ->get();

        $totalJamLembur = 0;

        foreach ($absensi as $absen) {
            $jamMasuk = Carbon::parse($absen->jam_masuk);
            $jamKeluar = Carbon::parse($absen->jam_keluar);
            $jamKerja = $jamMasuk->diffInHours($jamKeluar);

            if ($jamKerja > 8) {
                $totalJamLembur += ($jamKerja - 8);
            }
        }

        return $totalJamLembur;
    }

    private function hitungPotonganCuti($nama, $bulan, $tahun)
    {
        $totalCutiTahunIni = DB::table('cuti_karyawan')
            ->where('nama', $nama)
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_mulai', $tahun)
            ->whereRaw('MONTH(tanggal_mulai) <= ?', [$bulan])
            ->sum('jumlah_hari');

        $cutiBulanIni = DB::table('cuti_karyawan')
            ->where('nama', $nama)
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_mulai', $tahun)
            ->whereMonth('tanggal_mulai', $bulan)
            ->sum('jumlah_hari');

        if ($totalCutiTahunIni <= 12 || $cutiBulanIni <= 0) {
            return 0;
        }

        $cutiSebelumBulanIni = $totalCutiTahunIni - $cutiBulanIni;
        $hariTerpotong = 0;

        if ($cutiSebelumBulanIni >= 12) {
            $hariTerpotong = $cutiBulanIni;
        } else {
            $hariTerpotong = $totalCutiTahunIni - 12;
        }

        return $hariTerpotong * 100000;
    }

    public function userPayslip(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $daftarBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $periodeCari = $daftarBulan[$bulan] . ' ' . $tahun;
        $sessionUser = session('user');
        $userName = $sessionUser['nama_karyawan'] ?? null;

        if (!$userName) {
            return redirect()->route('login')->with('error', 'Sesi habis, silakan login kembali.');
        }

        $gaji = DB::table('penggajian')
            ->where('nama', $userName)
            ->where('periode_bulan', $periodeCari)
            ->first();

        $karyawan = DB::table('karyawan')->where('nama', $userName)->first();

        if ($gaji && !isset($gaji->pot_cuti)) {
            $gaji->pot_cuti = $this->hitungPotonganCuti($gaji->nama, $bulan, $tahun);
        }

        return view('user.payslip.payslip', compact('gaji', 'karyawan', 'daftarBulan', 'bulan', 'tahun', 'periodeCari'));
    }

    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $daftarBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $daftarTahun = range(date('Y'), date('Y') - 2);

        $data_gaji = DB::table('karyawan')
            ->leftJoin('penggajian', function ($join) use ($bulan, $tahun, $daftarBulan) {
                $join->on('karyawan.nama', '=', 'penggajian.nama')
                    ->where('penggajian.periode_bulan', '=', $daftarBulan[$bulan] . ' ' . $tahun);
            })
            ->select(
                'karyawan.id_karyawan',
                'karyawan.nama',
                'karyawan.jabatan',
                'karyawan.lulusan',
                'penggajian.gaji_pokok as gaji_db',
                'penggajian.tunjangan as tunjangan_db',
                'penggajian.lembur as nominal_lembur_db',
                'penggajian.total_lembur as total_jam_lembur_db',
                'penggajian.jht as jht_db',
                'penggajian.pph21 as pph21_db',
                'penggajian.bpjs as bpjs_db',
                'penggajian.pot_cuti as pot_cuti_db',
                'penggajian.status as status_db' // Ambil kolom status dari database!
            )
            ->get();

        foreach ($data_gaji as $gaji) {
            $totalJamLembur = $this->hitungJamLembur($gaji->nama, $bulan, $tahun);
            $gaji->total_jam_lembur = $gaji->total_jam_lembur_db ?? $totalJamLembur;
            $gaji->nominal_lembur = $gaji->nominal_lembur_db ?? ($gaji->total_jam_lembur * 50000);

            $gaji->gaji_pokok = $gaji->gaji_db ?? ($gaji->lulusan == 'S1' ? 5500000 : 4500000);
            $gaji->tunjangan = $gaji->tunjangan_db ?? 1000000;
            $gaji->pot_cuti = $gaji->pot_cuti_db ?? $this->hitungPotonganCuti($gaji->nama, $bulan, $tahun);

            // Set default ke 'belum' jika data baru belum tersimpan di DB
            $gaji->status = $gaji->status_db ?? 'belum';

            $bruto = $gaji->gaji_pokok + $gaji->tunjangan + $gaji->nominal_lembur;

            $gaji->jht = $gaji->jht_db ?? round($gaji->gaji_pokok * 0.02);
            $gaji->pph21 = $gaji->pph21_db ?? round($bruto * $this->getTerRate($bruto));
            $gaji->bpjs = $gaji->bpjs_db ?? round(min($bruto, 12000000) * 0.01);

            $gaji->total_diterima = $bruto - ($gaji->jht + $gaji->pph21 + $gaji->bpjs + $gaji->pot_cuti);
        }

        return view('admin.penggajian.penggajian_karyawan', compact('data_gaji', 'daftarBulan', 'daftarTahun', 'bulan', 'tahun'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gaji.*.gaji_pokok' => 'required',
            'gaji.*.tunjangan' => 'required',
            'gaji.*.lembur' => 'required',
            'bulan' => 'required|string',
            'tahun' => 'required|string',
        ]);

        $inputs = $request->input('gaji');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $daftarBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $periodeBulan = $daftarBulan[$bulan] . ' ' . $tahun;

        DB::beginTransaction();

        try {
            foreach ($inputs as $id_karyawan => $data) {
                $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();

                if ($karyawan) {
                    $gaji_pokok = (int)preg_replace('/[^0-9]/', '', $data['gaji_pokok']);
                    $tunjangan  = (int)preg_replace('/[^0-9]/', '', $data['tunjangan']);
                    $nominalLembur = (int)preg_replace('/[^0-9]/', '', $data['lembur']);
                    $totalJamLembur = $nominalLembur / 50000;

                    $jht = (int)preg_replace('/[^0-9]/', '', $data['jht']);
                    $pph21 = (int)preg_replace('/[^0-9]/', '', $data['pph21']);
                    $bpjs = (int)preg_replace('/[^0-9]/', '', $data['bpjs']);
                    $pot_cuti = (int)preg_replace('/[^0-9]/', '', $data['pot_cuti'] ?? 0);

                    // Ambil status dari input select form, default-nya 'belum'
                    $status_bayar = $data['status'] ?? 'belum';

                    $bruto = $gaji_pokok + $tunjangan + $nominalLembur;
                    $total_neto = $bruto - ($jht + $pph21 + $bpjs + $pot_cuti);

                    $saveData = [
                        'nama' => $karyawan->nama,
                        'periode_bulan' => $periodeBulan,
                        'gaji_pokok' => $gaji_pokok,
                        'tunjangan' => $tunjangan,
                        'lembur' => $nominalLembur,
                        'total_lembur' => $totalJamLembur,
                        'jht' => $jht,
                        'pph21' => $pph21,
                        'bpjs' => $bpjs,
                        'pot_cuti' => $pot_cuti,
                        'total_diterima' => $total_neto,
                        'status' => $status_bayar // Menyimpan status pembayaran yang dipilih ('belum' / 'sudah')
                    ];

                    $existing = DB::table('penggajian')
                        ->where('nama', $karyawan->nama)
                        ->where('periode_bulan', $periodeBulan)
                        ->first();

                    if ($existing) {
                        DB::table('penggajian')->where('id', $existing->id)->update($saveData);
                    } else {
                        DB::table('penggajian')->insert($saveData);
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data Payroll untuk periode ' . $periodeBulan . ' berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
