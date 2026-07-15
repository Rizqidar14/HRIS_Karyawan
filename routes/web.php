<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiUserController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\PenggajianKaryawanController;
use App\Http\Controllers\KlaimUserController;
use App\Http\Controllers\KlaimController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes - HRIS Premium (Dual Role Integration)
|--------------------------------------------------------------------------
*/

// --- Public Access ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// --- Protected Access (Harus Login) ---
Route::middleware(['web'])->group(function () {

    Route::group(['middleware' => function ($request, $next) {
        if (!session()->has('user') || !isset(session('user')['authenticated'])) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }
        return $next($request);
    }], function () {

        // =========================================================================
        // 1. DASHBOARD HUB (Pintu Utama)
        // =========================================================================
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // =========================================================================
        // 2. RUTE KARYAWAN (UserDashboardController & AbsensiUserController)
        // =========================================================================
        Route::prefix('user')->name('user.')->group(function () {

            // Dashboard User
            Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

            // Manajemen Absensi User
            Route::controller(AbsensiUserController::class)->group(function () {
                Route::get('/absensi', 'index')->name('absensi.index');
                Route::post('/absensi/submit', 'store')->name('absensi.submit');
                Route::get('/riwayat', 'riwayat')->name('absensi.riwayat');
                Route::post('/absensi/lembur', 'storeLembur')->name('absensi.lembur');
            });

            // Manajemen Cuti User
            Route::controller(App\Http\Controllers\CutiUserController::class)->group(function () {
                Route::get('/cuti', 'index')->name('cuti.index');
                Route::post('/cuti/store', 'store')->name('cuti.store');
            });

            // ========== MANAJEMEN KLAIM REIMBURSEMENT USER ==========
            Route::controller(KlaimUserController::class)->group(function () {
                Route::get('/klaim', 'index')->name('klaim.index');
                Route::post('/klaim/store', 'store')->name('klaim.store');
                Route::get('/klaim/riwayat', 'riwayat')->name('klaim.riwayat');
            });

            // Penggajian User
            Route::get('/payslip', [App\Http\Controllers\PenggajianKaryawanController::class, 'userPayslip'])->name('payslip.index');

            // Profile User
            Route::get('/profile', function () {
                $userSession = session('user');
                $karyawan = session('karyawan_data') ?? [];
                return view('user.profile', [
                    'karyawan' => (object)$karyawan,
                    'user' => $userSession
                ]);
            })->name('profile');
        });

        // =========================================================================
        // 3. RUTE ADMIN (DashboardController & Full Management)
        // =========================================================================
        Route::group(['middleware' => function ($request, $next) {
            if (session('user')['role'] !== 'admin') {
                return redirect()->route('user.dashboard')->with('error', 'Akses Admin Diperlukan!');
            }
            return $next($request);
        }], function () {

            // Fitur Dashboard Utama Admin (Data Real & Statistik)
            Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
            Route::get('/dashboard/karyawan/list', [DashboardController::class, 'getKaryawanList'])->name('dashboard.karyawan.list');

            Route::prefix('admin')->name('admin.')->group(function () {

                // --- MANAJEMEN KARYAWAN ---
                Route::controller(KaryawanController::class)->group(function () {
                    Route::get('/karyawan', 'index')->name('karyawan.data-karyawan');
                    Route::get('/data-karyawan', 'index')->name('karyawan.index');
                    Route::get('/karyawan/create', 'create')->name('karyawan.create');
                    Route::post('/karyawan', 'store')->name('karyawan.store');
                    Route::get('/karyawan/{id}', 'show')->name('karyawan.detail-karyawan');
                    Route::get('/karyawan/{id}/edit', 'edit')->name('karyawan.edit-karyawan');
                    Route::put('/karyawan/{id}', 'update')->name('karyawan.update');
                    Route::delete('/karyawan/{id}', 'destroy')->name('karyawan.destroy');
                    Route::get('/karyawan/{id}/edit-data', 'getEditData')->name('karyawan.edit-data');
                    Route::post('/karyawan/{id}/update-ajax', 'updateAjax')->name('karyawan.update-ajax');
                });

                // --- MANAJEMEN AKUN ---
                Route::prefix('akun')->name('buat-akun.')->controller(AkunController::class)->group(function () {
                    Route::get('/buat', 'showBuatAkunForm')->name('form');
                    Route::post('/simpan', 'storeBuatAkun')->name('store');
                    Route::get('/daftar', 'showDaftarAkun')->name('daftar');
                    Route::post('/toggle-status/{id}', 'toggleStatusAkun')->name('toggle-status');
                    Route::post('/reset-password/{id}', 'resetPasswordAjax')->name('reset-password-ajax');
                    Route::delete('/delete/{id}', 'destroy')->name('destroy');
                    Route::post('/update-ajax/{id}', 'updateAjax')->name('update-ajax');
                    Route::get('/user-detail/{id}', 'getUserDetail')->name('user-detail');
                    Route::post('/set-tab', 'setTab')->name('set-tab');
                });

                // --- MANAJEMEN ABSENSI (Admin) ---
                Route::prefix('absensi')->name('absensi.')->controller(AbsensiController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/riwayat', 'riwayat')->name('riwayat-absen');
                    Route::post('/store', 'store')->name('store');
                    Route::delete('/hapus/{id}', 'destroy')->name('hapus');
                });

                // --- MANAJEMEN CUTI (Admin) ---
                Route::prefix('cuti')->name('cuti.')->controller(CutiController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/update-status/{id}', 'updateStatus')->name('update-status');
                });

                // --- MANAJEMEN PENGGAJIAN (Admin) ---
                Route::controller(PenggajianKaryawanController::class)->group(function () {
                    Route::get('/penggajian', 'index')->name('penggajian.index');
                    Route::post('/penggajian/update', 'update')->name('penggajian.update');
                });

                // ========== MANAJEMEN REIMBURSEMENT ADMIN ==========
                Route::prefix('klaim')->name('klaim.')->group(function () {
                    Route::get('/', [App\Http\Controllers\KlaimController::class, 'adminIndex'])->name('index');
                    Route::get('/riwayat', [App\Http\Controllers\KlaimController::class, 'adminRiwayat'])->name('riwayat');
                    Route::post('/approve/{id}', [App\Http\Controllers\KlaimController::class, 'approve'])->name('approve');
                    Route::post('/reject/{id}', [App\Http\Controllers\KlaimController::class, 'reject'])->name('reject');
                });

                // --- SYSTEM MAINTENANCE ---
                Route::get('/clear-cache', function () {
                    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
                    return response()->json(['success' => true, 'message' => 'Cache Berhasil Dibersihkan!']);
                })->name('clear-cache');
            });
        });
    });
});

// Fallback Route
Route::fallback(function () {
    return redirect()->route('login');
});
