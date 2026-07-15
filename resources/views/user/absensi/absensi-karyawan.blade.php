<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi Digital | Portal Karyawan</title>

    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/absensi-user.css') }}">
</head>

<body>
    @include('partials.navbar-user')

    <div class="container-wrapper">
        <header class="mb-4">
            <h1 class="fw-800">Presensi Digital</h1>
            <p class="text-muted"><span class="pulse-icon"></span> Sistem memantau lokasi dan durasi kerja Anda secara otomatis.</p>
        </header>

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 mb-4 shadow-sm">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-4 mb-4 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row g-4">
            <!-- Kolom Kiri: Form Absensi -->
            <div class="col-lg-5">
                <div class="bento-card">
                    <h3 class="h5 mb-4 fw-700">Form Absensi</h3>

                    @if(!$absenHariIni || ($absenHariIni->status == 'hadir' && !$absenHariIni->jam_keluar))
                        <form action="{{ route('user.absensi.submit') }}" method="POST" id="absensiForm">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">

                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-2 text-uppercase">Status Kehadiran</label>
                                <select name="status" id="status" class="form-select custom-input">
                                    <option value="hadir">Hadir Bekerja</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="izin">Izin</option>
                                </select>
                            </div>

                            <div id="keteranganSection" style="display: none;" class="mb-4">
                                <label class="form-label fw-600 small text-muted">ALASAN (SAKIT/IZIN)</label>
                                <textarea name="keterangan_status" class="form-control custom-input" rows="3" placeholder="Berikan alasan singkat..."></textarea>
                            </div>

                            <div id="mapContainer" class="mb-4">
                                <div id="manualMap"></div>
                                <div id="locStatus" class="small mt-2 text-center text-primary fw-bold">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Mencari lokasi...
                                </div>
                            </div>

                            <button type="submit" id="btnSubmitAbsen" class="btn-blue-main shadow-sm">
                                <span>
                                    <i class="fas {{ $absenHariIni ? 'fa-sign-out-alt' : 'fa-sign-in-alt' }} me-2"></i>
                                    {{ $absenHariIni ? 'Check-out Sekarang' : 'Check-in Sekarang' }}
                                </span>
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                            <h5 class="fw-700">Presensi Selesai!</h5>

                            <div class="badge bg-light text-dark p-3 rounded-4 mb-3 border w-100 shadow-sm">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Total Jam Kerja</small>
                                <span class="h5 fw-bold text-primary">{{ $absenHariIni->total_jam ?? '-- jam -- menit' }}</span>
                            </div>

                            <p class="text-muted small mb-4">Check-out pukul: <strong>{{ $absenHariIni->jam_keluar ?? '-' }}</strong></p>

                            <hr class="my-4">

                            @if(isset($absenHariIni) && !$absenHariIni->lembur)
                                <h6 class="small fw-bold text-muted text-uppercase mb-2">Input Pekerjaan Lembur?</h6>
                                <button type="button" class="btn btn-outline-primary w-100 fw-bold rounded-pill py-2" data-bs-toggle="modal" data-bs-target="#modalLembur">
                                    <i class="fas fa-clock me-2"></i> Masukkan Data Lembur
                                </button>
                            @elseif(isset($absenHariIni) && $absenHariIni->lembur)
                                <div class="alert alert-info border-0 rounded-4 text-start">
                                    <small class="fw-bold d-block text-uppercase mb-1" style="font-size: 0.65rem;">Data Lembur Tersimpan:</small>
                                    <span class="small">{{ $absenHariIni->lembur }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat & Filter -->
            <div class="col-lg-7">
                <div class="bento-card">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <h3 class="h5 mb-0 fw-700">Aktivitas Terbaru</h3>

                        <form action="{{ route('user.absensi.index') }}" method="GET" class="d-flex gap-2">
                            <select name="bulan" class="form-select form-select-sm rounded-pill px-3" style="width: 130px; font-size: 0.8rem;">
                                @foreach($daftarBulan as $key => $nama)
                                    <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                            <select name="tahun" class="form-select form-select-sm rounded-pill px-3" style="width: 90px; font-size: 0.8rem;">
                                @foreach($daftarTahun as $t)
                                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm rounded-circle" style="width: 38px;">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Tabel Riwayat Absensi -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Total Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat_user as $row)
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                                        <td>{{ $row->jam_masuk ?? '-' }}</td>
                                        <td>{{ $row->jam_keluar ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = '';
                                                $statusText = ucfirst($row->status);
                                                if($row->status == 'hadir') $statusClass = 'bg-presence';
                                                elseif($row->status == 'sakit') $statusClass = 'bg-sakit';
                                                elseif($row->status == 'izin') $statusClass = 'bg-izin';
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>{{ $row->total_jam ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-alt me-2"></i> Belum ada data absensi untuk periode ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($riwayat_user->isEmpty())
                        <div class="text-center mt-4">
                            <i class="fas fa-clock fa-3x text-muted mb-2"></i>
                            <p class="text-muted small">Belum ada aktivitas presensi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lembur -->
    <div class="modal fade" id="modalLembur" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-700">Detail Pekerjaan Lembur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('user.absensi.lembur') }}" method="POST" id="formLembur">
                    @csrf
                    <div class="modal-body px-4">
                        <div class="alert alert-warning py-2 mb-3 border-0 small" style="border-radius: 12px;">
                            <i class="fas fa-info-circle me-1"></i> Durasi lembur dihitung otomatis sejak jam check-out (Maks. 2 Jam).
                        </div>
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Apa yang dikerjakan?</label>
                        <textarea name="keterangan" class="form-control custom-input" rows="4" placeholder="Contoh: Menyelesaikan laporan rekap..." required></textarea>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="submit" class="btn-blue-main">Simpan Data Lembur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="{{ asset('js/absensi-user.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading State untuk form
            const forms = ['absensiForm', 'formLembur'];
            forms.forEach(id => {
                const f = document.getElementById(id);
                if(f) {
                    f.addEventListener('submit', function() {
                        const btn = f.querySelector('button[type="submit"]');
                        if(btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                        }
                    });
                }
            });

            // Map & GPS
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const locStatus = document.getElementById('locStatus');

            if (document.getElementById('manualMap')) {
                const map = L.map('manualMap').setView([-6.2000, 106.8166], 17);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);
                const marker = L.marker([-6.2000, 106.8166]).addTo(map);

                if (navigator.geolocation) {
                    navigator.geolocation.watchPosition((pos) => {
                        const { latitude, longitude, accuracy } = pos.coords;
                        if(latInput) latInput.value = latitude;
                        if(lngInput) lngInput.value = longitude;
                        marker.setLatLng([latitude, longitude]);
                        map.setView([latitude, longitude], 17);
                        if(locStatus) {
                            locStatus.innerHTML = `<i class="fas fa-satellite-dish text-success"></i> Lokasi Terkunci (${Math.round(accuracy)}m)`;
                        }
                    }, (err) => {
                        if(locStatus) {
                            locStatus.innerHTML = `<i class="fas fa-exclamation-triangle text-danger"></i> Aktifkan GPS untuk Presensi`;
                        }
                    }, { enableHighAccuracy: true });
                } else {
                    if(locStatus) {
                        locStatus.innerHTML = `<i class="fas fa-exclamation-triangle text-danger"></i> Browser tidak mendukung GPS`;
                    }
                }
            }

            // Handle Status Change
            const statusSelect = document.getElementById('status');
            const keteranganSection = document.getElementById('keteranganSection');
            const mapContainer = document.getElementById('mapContainer');

            if(statusSelect && keteranganSection && mapContainer) {
                statusSelect.addEventListener('change', function() {
                    const isHadir = this.value === 'hadir';
                    keteranganSection.style.display = isHadir ? 'none' : 'block';
                    mapContainer.style.opacity = isHadir ? '1' : '0.3';
                });

                // Trigger initial state
                const isHadirAwal = statusSelect.value === 'hadir';
                keteranganSection.style.display = isHadirAwal ? 'none' : 'block';
                mapContainer.style.opacity = isHadirAwal ? '1' : '0.3';
            }
        });
    </script>
</body>

</html>
