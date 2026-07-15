<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan Cuti | Portal Karyawan</title>

    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/cuti-personal.css') }}">
</head>

<body>
    @include('partials.navbar-user')

    <div class="container-wrapper">
        <header class="mb-5">
            <h1 class="fw-bold text-dark">Manajemen Cuti</h1>
            <p class="text-muted">Halo, <strong>{{ session('user')['nama_karyawan'] ?? 'User' }}</strong>. Kelola pengajuan istirahat Anda di sini.</p>
        </header>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="bento-card">
                    <h5 class="mb-4"><i class="fas fa-edit me-2 text-primary"></i>Buat Pengajuan</h5>

                    <div class="quota-info">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-calendar-check text-primary me-2"></i>
                            <span class="small fw-bold text-dark">Sisa Jatah Cuti Tahunan</span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $sisaJatah }} <small class="text-muted" style="font-size: 0.9rem;">Hari</small></h3>
                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">
                            *Kelebihan jatah akan dikenakan potongan <strong>Rp 100.000/hari</strong>.
                        </p>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success border-0 small mb-3">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger border-0 small mb-3">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('user.cuti.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-2">NAMA KARYAWAN</label>
                            <input type="text" class="form-control custom-input" value="{{ session('user')['nama_karyawan'] ?? '' }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-2">JENIS CUTI</label>
                            <select name="jenis_cuti" class="form-select custom-input" required>
                                <option value="Cuti Tahunan">Cuti Tahunan</option>
                                <option value="Cuti Sakit">Cuti Sakit</option>
                                <option value="Izin Penting">Izin Penting</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">MULAI</label>
                                <input type="date" name="tanggal_mulai" class="form-control custom-input" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="small fw-bold text-muted mb-2">SELESAI</label>
                                <input type="date" name="tanggal_selesai" class="form-control custom-input" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-2">KETERANGAN / ALASAN</label>
                            <textarea name="keterangan" class="form-control custom-input" rows="3" placeholder="Jelaskan alasan cuti..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-2">LAMPIRAN (DOKUMEN/FOTO)</label>
                            <input type="file" name="lampiran_dokumen" class="form-control custom-input">
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="bento-card">
                    <h5 class="mb-4"><i class="fas fa-history me-2 text-primary"></i>Riwayat Pengajuan</h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat_cuti as $c)
                                <tr>
                                    <td class="small fw-500">
                                        {{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $c->jenis_cuti }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ Str::limit($c->keterangan, 40) }}</div>
                                    </td>
                                    <td class="small">{{ $c->jumlah_hari }} Hari</td>
                                    <td>
                                        <span class="status-badge bg-{{ strtolower($c->status) }}">
                                            {{ $c->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3 d-block opacity-25"></i>
                                        <span class="small">Belum ada riwayat pengajuan cuti.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
