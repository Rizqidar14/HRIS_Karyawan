<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Karyawan | HR System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/detail-karyawan.css') }}">

<div class="main-container">
    <div class="page-header">
        <div>
            <span class="breadcrumb-custom">Direktori / Profil Karyawan</span>
            <h1 class="page-title">Detail Personel</h1>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.karyawan.data-karyawan') }}" class="btn-back-soft">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
            <a href="{{ route('admin.karyawan.edit-karyawan', $karyawan->id) }}" class="btn btn-edit-premium">
                <i class="fas fa-user-pen me-2"></i> Edit Profil
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Profile -->
        <div class="col-xl-4 col-lg-5">
            <div class="glass-card profile-hero">
                <div class="image-container">
                    @if($karyawan->foto)
                        <img src="{{ asset('storage/' . $karyawan->foto) }}" class="profile-img" alt="User Profile">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama) }}&background=4361ee&color=fff&size=200" class="profile-img" alt="Avatar">
                    @endif
                    <span class="status-badge-floating">{{ $karyawan->status ?? 'Aktif' }}</span>
                </div>

                <h3 class="fw-800 mb-1">{{ $karyawan->nama }}</h3>
                <p class="text-primary fw-600 mb-4">{{ $karyawan->jabatan ?? 'General Staff' }}</p>

                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-3 border rounded-4 bg-light">
                            <small class="mini-label" style="font-size: 0.6rem;">Divisi</small>
                            <div class="fw-bold">{{ $karyawan->divisi }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 border rounded-4 bg-light">
                            <small class="mini-label" style="font-size: 0.6rem;">ID Akses</small>
                            <div class="fw-bold text-primary">#{{ $karyawan->id_karyawan }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Area -->
        <div class="col-xl-8 col-lg-7">
            <div class="glass-card">
                <h4 class="info-grid-title">
                    <i class="fas fa-address-card text-primary"></i> Data Personal & Pekerjaan
                </h4>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="data-card-mini">
                            <div class="mini-icon"><i class="fas fa-id-badge"></i></div>
                            <div>
                                <span class="mini-label">ID Karyawan</span>
                                <span class="mini-value">{{ $karyawan->id_karyawan }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="data-card-mini">
                            <div class="mini-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <span class="mini-label">Email Institusi</span>
                                <span class="mini-value text-lowercase">{{ $karyawan->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="data-card-mini">
                            <div class="mini-icon"><i class="fab fa-whatsapp"></i></div>
                            <div>
                                <span class="mini-label">Kontak Aktif</span>
                                <span class="mini-value">{{ $karyawan->telepon ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="data-card-mini">
                            <div class="mini-icon"><i class="fas fa-graduation-cap"></i></div>
                            <div>
                                <span class="mini-label">Pendidikan</span>
                                <span class="mini-value">{{ $karyawan->lulusan ?? 'Tidak diisi' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="data-card-mini">
                            <div class="mini-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <span class="mini-label">Alamat Domisili</span>
                                <span class="mini-value">{{ $karyawan->alamat ?? 'Lokasi belum diperbarui' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horizontal Stats -->
                <div class="stats-footer">
                    <div class="stat-item">
                        <h6>Masa Bakti</h6>
                        <p>{{ \Carbon\Carbon::parse($karyawan->tanggal_bergabung)->diffInYears() }} Tahun</p>
                    </div>
                    <div class="stat-item">
                        <h6>Pembaruan</h6>
                        <p>{{ \Carbon\Carbon::parse($karyawan->updated_at)->diffForHumans() }}</p>
                    </div>
                    <div class="stat-item">
                        <h6>Bergabung</h6>
                        <p>{{ \Carbon\Carbon::parse($karyawan->tanggal_bergabung)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
