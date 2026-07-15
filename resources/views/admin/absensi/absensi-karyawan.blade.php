<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Absensi - Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/absensi-karyawan.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <div class="container py-4">
        <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="text-center text-md-start mb-3 mb-md-0">
                <h1 class="fw-bold text-dark mb-1" style="font-size: clamp(1.75rem, 5vw, 2.5rem);">Manajemen Absensi</h1>
                <p class="text-muted small mb-0">Kelola presensi harian dan pantau aktivitas lembur secara real-time.</p>
            </div>
            <div class="text-center">
                <a href="{{ route('admin.absensi.riwayat-absen') }}" class="btn-history shadow-sm">
                    <i class="fas fa-history"></i> Riwayat Absensi
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box bg-success text-white me-3"><i class="fas fa-user-check"></i></div>
                    <div><small class="text-muted d-block small fw-600 uppercase">Hadir</small><h5 class="mb-0 fw-800 text-success">{{ $statistik['hadir'] }}</h5></div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box bg-warning text-white me-3"><i class="fas fa-user-clock"></i></div>
                    <div><small class="text-muted d-block small fw-600 uppercase">Izin/Sakit</small><h5 class="mb-0 fw-800 text-warning">{{ $statistik['izin'] + $statistik['sakit'] }}</h5></div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box bg-danger text-white me-3"><i class="fas fa-user-times"></i></div>
                    <div><small class="text-muted d-block small fw-600 uppercase">Belum Absen</small><h5 class="mb-0 fw-800 text-danger">{{ $statistik['belum_absen'] }}</h5></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="bento-card">
                    <h3 class="h5 mb-4 fw-700 text-primary"><i class="fas fa-edit me-2"></i>Presensi Manual</h3>
                    <form action="{{ route('admin.absensi.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Karyawan</label>
                            <select name="nama" class="form-select custom-input" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($karyawan as $k)
                                    <option value="{{ $k->nama }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Status</label>
                            <select name="status" id="statusSelect" class="form-select custom-input">
                                <option value="hadir">Hadir</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                        <div id="lemburArea" class="mb-3">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Keterangan Lembur</label>
                            <textarea name="lembur" class="form-control custom-input" rows="2" placeholder="Aktivitas lembur..."></textarea>
                        </div>
                        <div id="keteranganArea" class="mb-4" style="display: none;">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Alasan Keterangan</label>
                            <textarea name="keterangan" class="form-control custom-input" rows="2" placeholder="Detail alasan..."></textarea>
                        </div>
                        <button type="submit" class="btn-blue-main shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Data
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="bento-card">
                    <h3 class="h5 mb-4 fw-700"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Hari Ini</h3>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="small text-muted fw-bold text-uppercase border-bottom">
                                <tr>
                                    <th style="min-width: 150px;">Karyawan</th>
                                    <th>Jam</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensi as $a)
                                <tr class="border-bottom">
                                    <td class="py-3">
                                        <div class="fw-700">{{ $a->nama }}</div>
                                        @if($a->lembur)
                                            <div class="overtime-note">
                                                <i class="fas fa-moon me-1"></i> {{ $a->lembur }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small text-success fw-600"><i class="fas fa-arrow-right me-1"></i>{{ $a->jam_masuk ?? '--:--' }}</div>
                                        <div class="small text-danger fw-600"><i class="fas fa-arrow-left me-1"></i>{{ $a->jam_keluar ?? '--:--' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-{{ strtolower($a->status) }}">
                                            {{ $a->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-5 text-muted small italic">Belum ada aktivitas presensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/absensi-karyawan.js') }}"></script>
</body>
</html>
