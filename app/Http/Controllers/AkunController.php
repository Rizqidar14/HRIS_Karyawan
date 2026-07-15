<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    /**
     * Menampilkan form buat akun karyawan
     */
    public function showBuatAkunForm()
    {
        try {
            // Ambil daftar karyawan yang belum memiliki akun
            $karyawanWithoutAccount = DB::table('karyawan')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('users')
                        ->whereColumn('users.karyawan_id', 'karyawan.id');
                })
                ->select('karyawan.*')
                ->orderBy('karyawan.nama')
                ->get();

            Log::info('Menampilkan form buat akun. Jumlah karyawan tanpa akun: ' . $karyawanWithoutAccount->count());

            return view('admin.buat_akun.buat-akun', compact('karyawanWithoutAccount'));
        } catch (\Exception $e) {
            Log::error('Error di showBuatAkunForm: ' . $e->getMessage());
            return view('admin.buat_akun.buat-akun')->with([
                'karyawanWithoutAccount' => collect([]),
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menyimpan akun baru (untuk karyawan DAN admin)
     */
    public function storeBuatAkun(Request $request)
    {
        Log::info('===== MEMULAI PROSES STORE BUAT AKUN =====');
        Log::info('Request data: ', $request->all());

        // Cek apakah ini form admin atau karyawan
        $isAdminForm = $request->has('nama') && !$request->has('karyawan_id');

        if ($isAdminForm) {
            return $this->createAdmin($request);
        } else {
            return $this->createKaryawanAccount($request);
        }
    }

    /**
     * Membuat akun untuk karyawan yang sudah terdaftar
     * password = plain text, password_encrypted = hash
     */
    private function createKaryawanAccount(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'karyawan_id' => 'required|exists:karyawan,id',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:3',
            'password_confirmation' => 'required|same:password',
            'role' => 'required|in:karyawan',
        ], [
            'karyawan_id.required' => 'Pilih karyawan terlebih dahulu',
            'karyawan_id.exists' => 'Karyawan tidak ditemukan dalam database',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan, gunakan username lain',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 3 karakter',
            'password_confirmation.required' => 'Konfirmasi password harus diisi',
            'password_confirmation.same' => 'Password dan konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            Log::warning('Validasi gagal: ', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(', ', $validator->errors()->all()));
        }

        try {
            DB::beginTransaction();

            // Cek apakah karyawan sudah memiliki akun
            $existingAccount = DB::table('users')
                ->where('karyawan_id', $request->karyawan_id)
                ->exists();

            if ($existingAccount) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Karyawan ini sudah memiliki akun!')
                    ->withInput();
            }

            // Ambil data karyawan
            $karyawan = DB::table('karyawan')
                ->where('id', $request->karyawan_id)
                ->first();

            if (!$karyawan) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Data karyawan tidak ditemukan!')
                    ->withInput();
            }

            $plainPassword = $request->password;
            $hashedPassword = Hash::make($plainPassword);

            // Insert data akun
            // password = plain text, password_encrypted = hash
            $data = [
                'karyawan_id' => $request->karyawan_id,
                'nama_karyawan' => $karyawan->nama,
                'username' => $request->username,
                'password' => $plainPassword,  // Plain text
                'password_encrypted' => $hashedPassword,  // Hash
                'role' => 'karyawan',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('Data yang akan diinsert: ', [
                'karyawan_id' => $data['karyawan_id'],
                'nama_karyawan' => $data['nama_karyawan'],
                'username' => $data['username'],
                'password' => $data['password'],
                'password_encrypted' => '(hashed)',
                'role' => $data['role']
            ]);

            $inserted = DB::table('users')->insert($data);

            if ($inserted) {
                DB::commit();
                return redirect()->route('admin.buat-akun.daftar')
                    ->with('success', 'Akun untuk ' . $karyawan->nama . ' berhasil dibuat!<br>Username: <strong>' . $request->username . '</strong><br>Password: <strong>' . $plainPassword . '</strong>');
            } else {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Gagal menyimpan data akun ke database!')
                    ->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error di createKaryawanAccount: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Buat akun admin baru
     * password = plain text, password_encrypted = hash
     */
    public function createAdmin(Request $request)
    {
        Log::info('===== MEMULAI PROSES CREATE ADMIN =====');
        Log::info('Request data: ', $request->all());

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:3',
            'password_confirmation' => 'required|same:password',
            'role' => 'required|in:admin,karyawan',
        ], [
            'nama.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 3 karakter',
            'password_confirmation.required' => 'Konfirmasi password harus diisi',
            'password_confirmation.same' => 'Password dan konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(', ', $validator->errors()->all()));
        }

        try {
            DB::beginTransaction();

            $plainPassword = $request->password;
            $hashedPassword = Hash::make($plainPassword);

            // Data untuk insert
            // password = plain text, password_encrypted = hash
            $data = [
                'karyawan_id' => null,
                'nama_karyawan' => $request->nama,
                'username' => $request->username,
                'password' => $plainPassword,  // Plain text
                'password_encrypted' => $hashedPassword,  // Hash
                'role' => $request->role,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('Data admin yang akan diinsert: ', [
                'nama_karyawan' => $data['nama_karyawan'],
                'username' => $data['username'],
                'password' => $data['password'],
                'password_encrypted' => '(hashed)',
                'role' => $data['role']
            ]);

            $inserted = DB::table('users')->insert($data);

            if ($inserted) {
                DB::commit();
                return redirect()->route('admin.buat-akun.daftar')
                    ->with('success', 'Akun ' . ($request->role == 'admin' ? 'Administrator' : 'Karyawan') . ' berhasil dibuat!<br>Username: <strong>' . $request->username . '</strong><br>Password: <strong>' . $plainPassword . '</strong>');
            } else {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Gagal menyimpan data ke database')
                    ->withInput();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error di createAdmin: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan daftar akun
     */
    public function showDaftarAkun()
    {
        try {
            $users = DB::table('users')
                ->leftJoin('karyawan', 'users.karyawan_id', '=', 'karyawan.id')
                ->select(
                    'users.id',
                    'users.karyawan_id',
                    'users.nama_karyawan',
                    'users.username',
                    'users.password',
                    'users.password_encrypted',
                    'users.role',
                    'users.status',
                    'users.created_at',
                    'users.updated_at',
                    'karyawan.nama as nama_karyawan_data',
                    'karyawan.jabatan',
                    'karyawan.divisi',
                    'karyawan.email as email_karyawan',
                    'karyawan.telepon as no_telepon',
                    'karyawan.id_karyawan as nip'
                )
                ->orderBy('users.created_at', 'desc')
                ->get();

            foreach ($users as $user) {
                if ($user->role == 'karyawan' && $user->nama_karyawan_data) {
                    $user->nama_display = $user->nama_karyawan_data;
                } else {
                    $user->nama_display = $user->nama_karyawan ?: 'Admin';
                }

                $user->created_at_formatted = $user->created_at ? date('d M Y H:i', strtotime($user->created_at)) : '-';
            }

            $totalAkun = DB::table('users')->count();
            $adminCount = DB::table('users')->where('role', 'admin')->count();
            $karyawanCount = DB::table('users')->where('role', 'karyawan')->count();

            return view('admin.buat_akun.detail-akun', [
                'users' => $users,
                'totalAkun' => $totalAkun,
                'adminCount' => $adminCount,
                'karyawanCount' => $karyawanCount
            ]);
        } catch (\Exception $e) {
            Log::error('ERROR di showDaftarAkun: ' . $e->getMessage());
            return view('admin.buat_akun.detail-akun')->with([
                'users' => collect([]),
                'totalAkun' => 0,
                'adminCount' => 0,
                'karyawanCount' => 0,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset password via AJAX
     * password = plain text baru, password_encrypted = hash dari password baru
     */
    public function resetPasswordAjax($id)
    {
        try {
            DB::beginTransaction();

            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            // Generate random password (8 karakter)
            $randomPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            $hashedPassword = Hash::make($randomPassword);

            // Update password columns
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'password' => $randomPassword,  // Plain text baru
                    'password_encrypted' => $hashedPassword,  // Hash dari password baru
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset',
                'new_password' => $randomPassword
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error di resetPasswordAjax: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update akun via AJAX
     */
    public function updateAjax(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $currentUser = DB::table('users')->where('id', $id)->first();

            if (!$currentUser) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            $rules = [
                'username' => 'required|string|max:50',
                'status' => 'required|in:aktif,nonaktif',
            ];

            if (!$currentUser->karyawan_id) {
                $rules['nama_karyawan'] = 'required|string|max:255';
                $rules['role'] = 'required|in:admin,karyawan';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek username duplicate
            $existingUser = DB::table('users')
                ->where('username', $request->username)
                ->where('id', '!=', $id)
                ->first();

            if ($existingUser) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Username sudah digunakan'
                ], 422);
            }

            $data = [
                'username' => $request->username,
                'status' => $request->status,
                'updated_at' => now()
            ];

            if (!$currentUser->karyawan_id) {
                $data['nama_karyawan'] = $request->nama_karyawan;
                $data['role'] = $request->role;
            }

            // Jika ada password baru
            if ($request->filled('password')) {
                $passwordValidator = Validator::make($request->all(), [
                    'password' => 'min:3'
                ]);

                if ($passwordValidator->fails()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Password minimal 3 karakter'
                    ], 422);
                }

                $plainPassword = $request->password;
                $hashedPassword = Hash::make($plainPassword);

                $data['password'] = $plainPassword;  // Plain text
                $data['password_encrypted'] = $hashedPassword;  // Hash
            }

            DB::table('users')->where('id', $id)->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error di updateAjax: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle status akun
     */
    public function toggleStatusAkun($id)
    {
        try {
            DB::beginTransaction();

            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';

            DB::table('users')
                ->where('id', $id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status akun berhasil diubah menjadi ' . $newStatus,
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error di toggleStatusAkun: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus akun
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            DB::table('users')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error di destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user detail untuk modal AJAX
     */
    public function getUserDetail($id)
    {
        try {
            $user = DB::table('users')
                ->leftJoin('karyawan', 'users.karyawan_id', '=', 'karyawan.id')
                ->select(
                    'users.id',
                    'users.karyawan_id',
                    'users.nama_karyawan',
                    'users.username',
                    'users.password',
                    'users.role',
                    'users.status',
                    'users.created_at',
                    'users.updated_at',
                    'karyawan.nama as nama_karyawan_data',
                    'karyawan.jabatan',
                    'karyawan.divisi',
                    'karyawan.email as email_karyawan',
                    'karyawan.telepon as no_telepon',
                    'karyawan.id_karyawan as nip'
                )
                ->where('users.id', $id)
                ->first();

            if ($user) {
                if ($user->role == 'karyawan' && $user->nama_karyawan_data) {
                    $user->nama_display = $user->nama_karyawan_data;
                } else {
                    $user->nama_display = $user->nama_karyawan ?: 'Admin';
                }

                $user->created_at_formatted = $user->created_at ? date('d M Y H:i', strtotime($user->created_at)) : '-';

                return response()->json([
                    'success' => true,
                    'data' => $user
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error di getUserDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set tab aktif ke session
     */
    public function setTab(Request $request)
    {
        try {
            $validated = $request->validate([
                'tab' => 'required|in:karyawan,admin'
            ]);

            session(['active_tab' => $request->tab]);

            return response()->json([
                'success' => true,
                'message' => 'Tab saved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error di setTab: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save tab: ' . $e->getMessage()
            ], 500);
        }
    }
}
