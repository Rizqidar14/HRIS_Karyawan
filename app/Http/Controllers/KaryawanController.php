<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $karyawan = DB::table('karyawan')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Hitung statistik untuk ditampilkan di halaman
            $totalKaryawan = DB::table('karyawan')->count();
            $karyawanAktif = DB::table('karyawan')->where('status', 'Aktif')->count();
            $karyawanBaru = DB::table('karyawan')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->count();
            $totalDivisi = DB::table('karyawan')
                ->select('divisi')
                ->distinct()
                ->count();

            return view('admin.karyawan.data-karyawan', compact(
                'karyawan',
                'totalKaryawan',
                'karyawanAktif',
                'karyawanBaru',
                'totalDivisi'
            ));
        } catch (\Exception $e) {
            Log::error('KaryawanController index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat data karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.karyawan.detail');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi input - Ditambahkan validasi 'lulusan'
            $validated = $request->validate([
                'id_karyawan' => 'required|string|max:50|unique:karyawan,id_karyawan',
                'nama' => 'required|string|max:255',
                'divisi' => 'required|string|max:100',
                'jabatan' => 'required|string|max:100',
                'email' => 'required|email|unique:karyawan,email',
                'telepon' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'tanggal_bergabung' => 'required|date',
                'status' => 'required|in:Aktif,Non-Aktif,Cuti',
                'lulusan' => 'required|in:SMK,S1,S2,S3', // New Field
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            // Siapkan data untuk insert - Ditambahkan 'lulusan'
            $data = [
                'id_karyawan' => $request->id_karyawan,
                'nama' => $request->nama,
                'divisi' => $request->divisi,
                'jabatan' => $request->jabatan,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'tanggal_bergabung' => $request->tanggal_bergabung,
                'status' => $request->status,
                'lulusan' => $request->lulusan, // New Data Entry
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Handle upload foto
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                if ($file->isValid()) {
                    $filename = 'karyawan_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('karyawan/foto', $filename, 'public');
                    $data['foto'] = $path;
                }
            }

            // Insert ke database
            DB::table('karyawan')->insert($data);

            return redirect()->route('admin.karyawan.data-karyawan')
                ->with('success', 'Data karyawan berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('KaryawanController store error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $karyawan = DB::table('karyawan')->where('id', $id)->first();

            if (!$karyawan) {
                return redirect()->route('admin.karyawan.data-karyawan')
                    ->with('error', 'Data karyawan tidak ditemukan');
            }

            return view('admin.karyawan.detail-karyawan', compact('karyawan'));
        } catch (\Exception $e) {
            Log::error('KaryawanController show error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat detail karyawan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $karyawan = DB::table('karyawan')->where('id', $id)->first();

            if (!$karyawan) {
                return redirect()->route('admin.karyawan.data-karyawan')
                    ->with('error', 'Data karyawan tidak ditemukan');
            }

            return view('admin.karyawan.edit-karyawan', compact('karyawan'));
        } catch (\Exception $e) {
            Log::error('KaryawanController edit error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat form edit');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $karyawan = DB::table('karyawan')->where('id', $id)->first();

            if (!$karyawan) {
                return redirect()->route('admin.karyawan.data-karyawan')
                    ->with('error', 'Data karyawan tidak ditemukan');
            }

            // Validasi Update - Ditambahkan 'lulusan'
            $request->validate([
                'id_karyawan' => 'required|unique:karyawan,id_karyawan,' . $id,
                'nama' => 'required|string|max:255',
                'divisi' => 'required|string|max:100',
                'jabatan' => 'required|string|max:100',
                'email' => 'required|email|unique:karyawan,email,' . $id,
                'telepon' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'tanggal_bergabung' => 'required|date',
                'status' => 'required|in:Aktif,Non-Aktif,Cuti',
                'lulusan' => 'required|in:SMK,S1,S2,S3', // New Field
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $data = [
                'id_karyawan' => $request->id_karyawan,
                'nama' => $request->nama,
                'divisi' => $request->divisi,
                'jabatan' => $request->jabatan,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'tanggal_bergabung' => $request->tanggal_bergabung,
                'status' => $request->status,
                'lulusan' => $request->lulusan, // New Data Entry
                'updated_at' => now()
            ];

            // Handle foto upload
            if ($request->hasFile('foto')) {
                // Delete old foto
                if ($karyawan->foto) {
                    Storage::disk('public')->delete($karyawan->foto);
                }

                $file = $request->file('foto');
                $filename = 'karyawan_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('karyawan/foto', $filename, 'public');
                $data['foto'] = $path;
            }

            DB::table('karyawan')->where('id', $id)->update($data);

            return redirect()->route('admin.karyawan.data-karyawan')
                ->with('success', 'Data karyawan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('KaryawanController update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Update data via AJAX
     */
    public function updateAjax(Request $request, $id)
    {
        try {
            $karyawan = DB::table('karyawan')->where('id', $id)->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            $data = [
                'id_karyawan' => $request->id_karyawan ?? $karyawan->id_karyawan,
                'nama' => $request->nama ?? $karyawan->nama,
                'divisi' => $request->divisi ?? $karyawan->divisi,
                'jabatan' => $request->jabatan ?? $karyawan->jabatan,
                'email' => $request->email ?? $karyawan->email,
                'telepon' => $request->telepon ?? $karyawan->telepon,
                'alamat' => $request->alamat ?? $karyawan->alamat,
                'tanggal_bergabung' => $request->tanggal_bergabung ?? $karyawan->tanggal_bergabung,
                'status' => $request->status ?? $karyawan->status,
                'lulusan' => $request->lulusan ?? $karyawan->lulusan, // Handle Ajax update
                'updated_at' => now()
            ];

            // Handle foto upload if exists
            if ($request->hasFile('foto')) {
                if ($karyawan->foto) {
                    Storage::disk('public')->delete($karyawan->foto);
                }

                $file = $request->file('foto');
                $filename = 'karyawan_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('karyawan/foto', $filename, 'public');
                $data['foto'] = $path;
            }

            DB::table('karyawan')->where('id', $id)->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('KaryawanController updateAjax error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get edit data for AJAX
     */
    public function getEditData($id)
    {
        try {
            $karyawan = DB::table('karyawan')->where('id', $id)->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $karyawan
            ]);
        } catch (\Exception $e) {
            Log::error('KaryawanController getEditData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $karyawan = DB::table('karyawan')->where('id', $id)->first();

            if (!$karyawan) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data karyawan tidak ditemukan'
                    ], 404);
                }
                return redirect()->route('admin.karyawan.data-karyawan')
                    ->with('error', 'Data karyawan tidak ditemukan');
            }

            // Delete foto if exists
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            DB::table('karyawan')->where('id', $id)->delete();

            // Cek apakah request dari AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data karyawan berhasil dihapus'
                ]);
            }

            return redirect()->route('admin.karyawan.data-karyawan')
                ->with('success', 'Data karyawan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('KaryawanController destroy error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.karyawan.data-karyawan')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
