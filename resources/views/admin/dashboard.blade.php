<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HRIS Plus</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <main class="dashboard-container">
        <!-- Header Ringkas -->
        <div class="hero-welcome">
            <div>
                <h2>Halo, {{ session('user')['nama_karyawan'] ?? 'Admin' }}! 👋</h2>
                <p class="mb-0 opacity-75 small">Sistem memantau <strong>{{ $pengajuanCutiPending }}</strong> pengajuan baru.</p>
            </div>
            <div class="live-clock">
                <span id="current-time">00:00:00</span>
                <span id="current-date">Memuat...</span>
            </div>
        </div>

        <!-- Statistik Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-label">Total Karyawan</span>
                <span class="stat-value">{{ $totalKaryawan }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Hadir Hari Ini</span>
                <span class="stat-value text-success">{{ $hadirHariIni }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Pending Cuti</span>
                <span class="stat-value text-warning">{{ $pengajuanCutiPending }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Terlambat</span>
                <span class="stat-value text-danger">{{ $terlambatHariIni }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Total Akun</span>
                <span class="stat-value">{{ $totalAkun }}</span>
            </div>
        </div>

        <div class="row g-3">
            <!-- Kehadiran Terbaru -->
            <div class="col-12 col-lg-6">
                <div class="content-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center p-3 border-bottom border-secondary border-opacity-10">
                        <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">Kehadiran Terbaru</h6>
                        <a href="{{ route('admin.absensi.riwayat-absen') }}" class="text-white opacity-50 small text-decoration-none" style="font-size: 0.7rem;">Lihat Semua</a>
                    </div>

                    @if($aktivitasTerbaru->isEmpty())
                        <div class="p-4 text-center d-flex flex-column align-items-center justify-content-center h-100" style="min-height: 200px;">
                            <div class="bg-secondary bg-opacity-10 p-3 rounded-circle mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-slash opacity-50 fs-4"></i>
                            </div>
                            <p class="small opacity-50 mb-0">Belum ada data kehadiran untuk hari ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    @foreach($aktivitasTerbaru->take(4) as $item)
                                    <tr>
                                        <td class="px-3 py-3 border-0">
                                            <div class="fw-bold" style="font-size: 0.85rem;">{{ $item->nama_karyawan ?? $item->nama }}</div>
                                            <div class="opacity-50" style="font-size: 0.7rem;">
                                                @if(in_array(strtolower($item->status), ['hadir', 'terlambat']))
                                                    Masuk: {{ $item->jam_masuk ?? '08:00' }}
                                                @else
                                                    Keterangan: {{ ucfirst($item->status) }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end px-3 py-3 border-0">
                                            @if(strtolower($item->status) == 'hadir')
                                                <span class="badge bg-success" style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase;">HADIR</span>
                                            @elseif(strtolower($item->status) == 'terlambat')
                                                <span class="badge bg-warning text-dark" style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase;">TERLAMBAT</span>
                                            @elseif(strtolower($item->status) == 'izin')
                                                <span class="badge bg-info text-dark" style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase;">IZIN</span>
                                            @elseif(strtolower($item->status) == 'sakit')
                                                <span class="badge bg-danger" style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase;">SAKIT</span>
                                            @else
                                                <span class="badge bg-secondary" style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase;">{{ $item->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Panel Cuti -->
            <div class="col-12 col-lg-6">
                <div class="content-card h-100">
                    <div class="card-header p-3 border-bottom border-secondary border-opacity-10">
                        <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">Pengajuan Cuti</h6>
                    </div>
                    <div class="p-4 text-center d-flex flex-column align-items-center justify-content-center h-100" style="min-height: 150px;">
                        @if($pengajuanCutiPending > 0)
                            <div class="fw-bold text-warning mb-1" style="font-size: 2rem;">{{ $pengajuanCutiPending }}</div>
                            <p class="small opacity-75 mb-3">Dokumen menunggu persetujuan Anda</p>
                            <a href="{{ route('admin.cuti.index') }}" class="btn btn-sm btn-light w-100 max-width-200" style="border-radius: 10px; font-weight: 700;">Review Pengajuan</a>
                        @else
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle mb-3">
                                <i class="fas fa-check-circle text-success fs-3"></i>
                            </div>
                            <p class="small opacity-50 mb-0">Tidak ada pengajuan cuti tertunda.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    
    <script>
        function updateClock() {
            const now = new Date();
            const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

            const timeEl = document.getElementById('current-time');
            const dateEl = document.getElementById('current-date');

            if(timeEl) timeEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
            if(dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', optionsDate);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
