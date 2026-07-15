<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Klaim Karyawan | Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/klaim-karyawan.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <div class="container-wrapper">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-hand-holding-dollar text-primary me-2"></i>
                Klaim Karyawan
            </h3>
            <a href="{{ route('admin.klaim.riwayat') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-history me-2"></i> Riwayat Klaim
            </a>
        </div>

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-warning">{{ $statistik['pending'] ?? 0 }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-success">{{ $statistik['approved'] ?? 0 }}</h3>
                    <small class="text-muted">Disetujui</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-danger">{{ $statistik['rejected'] ?? 0 }}</h3>
                    <small class="text-muted">Ditolak</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-info">{{ $statistik['paid'] ?? 0 }}</h3>
                    <small class="text-muted">Dibayar</small>
                </div>
            </div>
        </div>

        <!-- Daftar Klaim Pending -->
        <div class="card-custom">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-clock text-warning me-2"></i>
                Klaim Menunggu Persetujuan
            </h5>

            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control search-box" placeholder="Cari no. klaim / karyawan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Filter</button>
                    <a href="{{ route('admin.klaim.index') }}" class="btn btn-secondary rounded-pill">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>No. Klaim</th>
                            <th>Karyawan</th>
                            <th>Divisi</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($klaim as $row)
                            <tr>
                                <td><strong>{{ $row->nomor_klaim }}</strong></td>
                                <td>{{ $row->nama_karyawan }}</td>
                                <td>{{ $row->divisi }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_klaim)->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($row->kategori) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($row->deskripsi, 30) }}</td>
                                <td class="fw-bold text-primary">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-circle me-1" title="Setujui" data-bs-toggle="modal" data-bs-target="#approveModal{{ $row->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $row->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Approve -->
                            <div class="modal fade" id="approveModal{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('admin.klaim.approve', $row->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header border-0">
                                                <h5 class="fw-bold">Setujui Klaim</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Setujui klaim dari <strong>{{ $row->nama_karyawan }}</strong>?</p>
                                                <p><small>Nominal: Rp {{ number_format($row->nominal, 0, ',', '.') }}</small></p>
                                                <textarea name="catatan" class="form-control rounded-3" rows="3" placeholder="Catatan (opsional)"></textarea>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success rounded-pill">Setujui</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Reject -->
                            <div class="modal fade" id="rejectModal{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('admin.klaim.reject', $row->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content rounded-4">
                                            <div class="modal-header border-0">
                                                <h5 class="fw-bold">Tolak Klaim</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Tolak klaim dari <strong>{{ $row->nama_karyawan }}</strong>?</p>
                                                <textarea name="alasan" class="form-control rounded-3" rows="3" required placeholder="Alasan penolakan..."></textarea>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger rounded-pill">Tolak</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                                    Tidak ada klaim pending
                                 </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $klaim->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
