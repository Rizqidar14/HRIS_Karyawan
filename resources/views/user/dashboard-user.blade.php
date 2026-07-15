<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan - HRIS Plus</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard-user.css') }}">
</head>

<body>

    @include('partials.navbar-user')

    <main class="dashboard-container">
        <!-- Section Hero -->
        <section class="welcome-hero">
            <div class="hero-text">
                <h1>Selamat Datang, {{ $userName ?? 'Karyawan' }}!</h1>
                <p>Pantau kehadiran Anda dengan latar yang lebih segar.</p>

                <div class="today-status">
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 5px;">STATUS HARI INI</div>
                    <div class="value">
                        <span class="status-badge
                            @if(isset($statusHariIni) && $statusHariIni == 'hadir') badge-hadir
                            @elseif(isset($statusHariIni) && $statusHariIni == 'sakit') badge-sakit
                            @elseif(isset($statusHariIni) && $statusHariIni == 'izin') badge-izin
                            @else badge-belum
                            @endif">
                            {{ $statusHariIni ?? 'Belum Absen' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="hero-action">
                <a href="{{ route('user.absensi.index') }}" style="text-decoration: none;">
                    <button style="background: #3b82f6; color: white; border: none; padding: 16px 32px; border-radius: 14px; font-weight: 800; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);">
                        <i class="fa-solid fa-location-dot me-2"></i> Presensi Sekarang
                    </button>
                </a>
            </div>
        </section>

        <!-- Grid Statistik[cite: 4] -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
                <div class="stat-info">
                    <h3>Kehadiran Bulan Ini</h3>
                    <div class="stat-number">{{ $kehadiranBulanIni ?? 0 }} / {{ $totalHariKerja ?? 0 }} Hari</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: {{ $persentaseKehadiran ?? 0 }}%"></div>
                    </div>
                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 10px;">
                        {{ $persentaseKehadiran ?? 0 }}% tingkat kehadiran
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1);"><i class="fa-solid fa-bed"></i></div>
                <div class="stat-info">
                    <h3>Sakit</h3>
                    <div class="stat-number">{{ $sakitBulanIni ?? 0 }} <span style="font-size: 1rem; color: #94a3b8;">Hari</span></div>
                    <p style="font-size: 0.8rem; color: #94a3b8;">Total catatan sakit</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);"><i class="fa-solid fa-envelope-open-text"></i></div>
                <div class="stat-info">
                    <h3>Izin</h3>
                    <div class="stat-number">{{ $izinBulanIni ?? 0 }} <span style="font-size: 1rem; color: #94a3b8;">Hari</span></div>
                    <p style="font-size: 0.8rem; color: #94a3b8;">Total catatan izin</p>
                </div>
            </div>
           <div class="stat-card">
                <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Cuti</h3>
                    <div class="stat-number">{{ $cutiBulanIni ?? 0 }} <span style="font-size: 1rem; color: #94a3b8;">Hari</span></div>
                    <p style="font-size: 0.8rem; color: #94a3b8;">Total catatan cuti</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
