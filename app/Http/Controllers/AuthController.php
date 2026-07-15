<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan Form Login
     */
    public function showLoginForm()
    {
        // Jika sudah login, langsung lempar ke dashboard pusat
        if (session()->has('user')) {
            return redirect()->route('dashboard');
        }

        // Memanggil view login di resources/views/login.blade.php
        return view('login');
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Cari user di database
        $user = DB::table('users')->where('username', $credentials['username'])->first();

        // 2. Cek apakah user ada dan password (Plain Text) cocok
        if ($user && $credentials['password'] === $user->password) {

            // Ambil nama karyawan dari tabel karyawan jika ada relasi
            $namaKaryawan = $user->nama_karyawan ?? $user->username;

            // Jika ada field karyawan_id, cari nama dari tabel karyawan
            if (isset($user->karyawan_id) && $user->karyawan_id) {
                $karyawan = DB::table('karyawan')->where('id', $user->karyawan_id)->first();
                if ($karyawan) {
                    $namaKaryawan = $karyawan->nama;
                }
            }

            // 3. Simpan data lengkap ke session
            $request->session()->put('user', [
                'id_user' => $user->id,
                'karyawan_id' => $user->karyawan_id ?? null,
                'nama_karyawan' => $namaKaryawan,
                'username' => $user->username,
                'role' => $user->role, // 'admin' atau 'karyawan'
                'authenticated' => true,
            ]);

            $request->session()->regenerate();

            // 4. Redirect ke rute dashboard pusat (Hub)
            return redirect()->route('dashboard');
        }

        // Jika gagal, kembalikan dengan pesan error
        return back()->withErrors([
            'error' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        // Hapus semua data session
        $request->session()->forget(['user', 'karyawan_data']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
