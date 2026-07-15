<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        try {
            $today = Carbon::today()->format('Y-m-d');

            // Ambil absensi hari ini dari tabel 'absen'
            $absensi = DB::table('absen')
                ->whereDate('tanggal_masuk', $today)
                ->orderBy('jam_masuk', 'desc')
                ->get();

            // Ambil karyawan dari tabel 'karyawan'
            $karyawan = DB::table('karyawan')->select('nama')->orderBy('nama')->get();

            $totalKaryawan = DB::table('karyawan')->count();
            $totalAbsen = DB::table('absen')->whereDate('tanggal_masuk', $today)->count();

            $statistik = [
                'hadir' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'hadir')->count(),
                'sakit' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'sakit')->count(),
                'izin' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'izin')->count(),
                'alpha' => DB::table('absen')->whereDate('tanggal_masuk', $today)->where('status', 'alpha')->count(),
                'belum_absen' => max(0, $totalKaryawan - $totalAbsen)
            ];

            return view('admin.absensi.absensi-karyawan', compact('absensi', 'karyawan', 'statistik'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi dasar
            $rules = [
                'nama' => 'required|string',
                'status' => 'required|in:hadir,sakit,izin',
                'action_type' => 'required|in:checkin,checkout',
            ];

            $messages = [
                'nama.required' => 'Nama karyawan wajib dipilih',
                'status.required' => 'Status wajib dipilih',
            ];

            // Validasi untuk status HADIR (wajib lokasi)
            if ($request->status === 'hadir') {
                $rules['latitude'] = 'required|numeric|not_in:0';
                $rules['longitude'] = 'required|numeric|not_in:0';
                $messages['latitude.required'] = 'Lokasi wajib diambil untuk status HADIR';
                $messages['latitude.not_in'] = 'Lokasi wajib diambil untuk status HADIR';
                $messages['longitude.required'] = 'Lokasi wajib diambil untuk status HADIR';
                $messages['longitude.not_in'] = 'Lokasi wajib diambil untuk status HADIR';
            }

            // Validasi untuk status IZIN/SAKIT saat check-in
            if ($request->action_type === 'checkin' && in_array($request->status, ['izin', 'sakit'])) {
                $rules['keterangan'] = 'required|string|min:3|max:500';
                $messages['keterangan.required'] = 'Keterangan wajib diisi untuk status ' . ucfirst($request->status);
                $messages['keterangan.min'] = 'Keterangan minimal 3 karakter';
                $messages['keterangan.max'] = 'Keterangan maksimal 500 karakter';
            }

            $request->validate($rules, $messages);

            $today = Carbon::today()->format('Y-m-d');
            $now = Carbon::now();

            // Format lokasi
            if ($request->status === 'hadir' && $request->filled('alamat_map') && $request->alamat_map !== '-') {
                $lokasi = $request->alamat_map;
            } else if ($request->status === 'hadir') {
                $lokasi = 'Lat: ' . $request->latitude . ', Lng: ' . $request->longitude;
            } else {
                $lokasi = 'Tidak memerlukan lokasi (Izin/Sakit)';
            }

            // Cek apakah sudah ada absensi hari ini
            $existingAbsen = DB::table('absen')
                ->where('nama', $request->nama)
                ->whereDate('tanggal_masuk', $today)
                ->first();

            // ACTION CHECK-IN
            if ($request->action_type === 'checkin') {
                if ($existingAbsen) {
                    return redirect()->back()->with('error', 'Karyawan sudah melakukan check-in hari ini!');
                }

                // Siapkan data untuk insert
                $insertData = [
                    'nama' => $request->nama,
                    'tanggal_masuk' => $today,
                    'jam_masuk' => $now->format('H:i:s'),
                    'status' => $request->status,
                    'lokasi_masuk' => $lokasi,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Tambahkan keterangan jika ada (untuk status izin/sakit)
                if ($request->filled('keterangan')) {
                    $insertData['keterangan_status'] = $request->keterangan;
                }

                // Untuk status izin/sakit, langsung set jam_keluar agar dianggap selesai
                if (in_array($request->status, ['izin', 'sakit'])) {
                    $insertData['jam_keluar'] = $now->format('H:i:s');
                    $insertData['lokasi_keluar'] = 'Tidak memerlukan checkout';
                }

                DB::table('absen')->insert($insertData);

                $statusText = ucfirst($request->status);
                $keteranganInfo = $request->filled('keterangan') ? " dengan keterangan: " . $request->keterangan : "";

                if (in_array($request->status, ['izin', 'sakit'])) {
                    return redirect()->back()->with('success', '✅ Absensi ' . $statusText . ' berhasil untuk ' . $request->nama . $keteranganInfo . ' (Tidak perlu checkout)');
                } else {
                    return redirect()->back()->with('success', '✅ Check-in berhasil untuk ' . $request->nama . ' | Pukul ' . $now->format('H:i:s'));
                }
            }

            // ACTION CHECK-OUT - HANYA UNTUK STATUS HADIR
            else if ($request->action_type === 'checkout') {
                if (!$existingAbsen) {
                    return redirect()->back()->with('error', 'Data absensi tidak ditemukan untuk check-out!');
                }

                if ($existingAbsen->status !== 'hadir') {
                    return redirect()->back()->with('error', 'Karyawan dengan status ' . ucfirst($existingAbsen->status) . ' tidak perlu melakukan check-out!');
                }

                if ($existingAbsen->jam_keluar) {
                    return redirect()->back()->with('error', 'Karyawan sudah melakukan check-out sebelumnya pada pukul ' . $existingAbsen->jam_keluar);
                }

                // Update data absen
                $updated = DB::table('absen')
                    ->where('id', $existingAbsen->id)
                    ->update([
                        'jam_keluar' => $now->format('H:i:s'),
                        'lokasi_keluar' => $lokasi,
                        'updated_at' => $now,
                    ]);

                if ($updated) {
                    // Hitung durasi kerja
                    $jamMasuk = Carbon::parse($existingAbsen->jam_masuk);
                    $jamKeluar = $now;
                    $durasi = $jamMasuk->diff($jamKeluar);
                    $durasiText = '';

                    if ($durasi->h > 0) {
                        $durasiText .= $durasi->h . ' jam ';
                    }
                    if ($durasi->i > 0) {
                        $durasiText .= $durasi->i . ' menit';
                    }
                    if (empty($durasiText)) {
                        $durasiText = 'kurang dari 1 menit';
                    }

                    return redirect()->back()->with('success', '✅ Check-out berhasil untuk ' . $request->nama . ' pada pukul ' . $now->format('H:i:s') . ' | Durasi: ' . $durasiText);
                } else {
                    return redirect()->back()->with('error', 'Gagal menyimpan check-out, silakan coba lagi');
                }
            }

            return redirect()->back()->with('error', 'Aksi tidak valid');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        try {
            $bulan = [
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

            $tahun_sekarang = date('Y');
            $tahun = range($tahun_sekarang, $tahun_sekarang - 5);

            $query = DB::table('absen')->orderBy('tanggal_masuk', 'desc')->orderBy('jam_masuk', 'desc');

            if ($request->filled('nama')) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            }
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal_masuk', $request->tanggal);
            }
            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal_masuk', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $query->whereYear('tanggal_masuk', $request->tahun);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $absensiData = $query->paginate(10);

            return view('admin.absensi.riwayat-absen', [
                'absensi' => $absensiData,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat riwayat: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $absen = DB::table('absen')->where('id', $id)->first();
            if ($absen) {
                DB::table('absen')->where('id', $id)->delete();
                return redirect()->back()->with('success', 'Data absensi ' . $absen->nama . ' berhasil dihapus.');
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
