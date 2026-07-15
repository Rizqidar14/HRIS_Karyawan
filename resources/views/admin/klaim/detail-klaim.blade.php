<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Klaim | Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/detail-klaim.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <div class="container-wrapper">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-hand-holding-dollar text-primary me-2"></i>
                Manajemen Klaim Reimbursement
            </h3>
        </div>

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0">{{ $statistik['total'] ?? 0 }}</h3>
                    <small class="text-muted">Total Klaim</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-warning">{{ $statistik['pending'] ?? 0 }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-success">{{ $statistik['approved'] ?? 0 }}</h3>
                    <small class="text-muted">Disetujui</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-info">{{ $statistik['paid'] ?? 0 }}</h3>
                    <small class="text-muted">Dibayar</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-danger">{{ $statistik['rejected'] ?? 0 }}</h3>
                    <small class="text-muted">Ditolak</small>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card">
                    <h3 class="fw-bold mb-0 text-primary">Rp {{ number_format($statistik['total_nominal'] ?? 0, 0, ',', '.') }}</h3>
                    <small class="text-muted">Total Nominal</small>
                </div>
            </div>
        </div>

        <!-- Jika $klaim ada (halaman daftar) -->
        @if(isset($klaim) && !isset($klaim->id))

        <!-- Filter -->
        <div class="card-custom">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select rounded-pill">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control search-box" placeholder="Cari no. klaim / karyawan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Filter</button>
                    <a href="{{ route('admin.klaim.index') }}" class="btn btn-secondary rounded-pill">Reset</a>
                </div>
            </form>
        </div>

        <!-- Tabel Klaim -->
        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>No. Klaim</th>
                            <th>Karyawan</th>
                            <th>Divisi</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Status</th>
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
                                <td class="fw-bold text-primary">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $class = '';
                                        if($row->status == 'pending') $class = 'status-pending';
                                        elseif($row->status == 'approved') $class = 'status-approved';
                                        elseif($row->status == 'rejected') $class = 'status-rejected';
                                        else $class = 'status-paid';
                                    @endphp
                                    <span class="status-badge {{ $class }}">{{ ucfirst($row->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.klaim.detail', $row->id) }}" class="btn btn-sm btn-outline-info rounded-circle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">Belum ada data klaim</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $klaim->links() }}
        </div>

        <!-- Jika $klaim adalah objek tunggal (halaman detail) -->
        @elseif(isset($klaim) && isset($klaim->id))

        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-receipt text-primary me-2"></i>
                    Detail Klaim
                </h4>
                <a href="{{ route('admin.klaim.index') }}" class="btn btn-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><td width="150"><strong>No. Klaim</strong></td><td>: {{ $klaim->nomor_klaim }}</td></tr>
                        <tr><td><strong>Nama Karyawan</strong></td><td>: {{ $klaim->nama_karyawan }}</td></tr>
                        <tr><td><strong>Divisi</strong></td><td>: {{ $klaim->divisi }}</td></tr>
                        <tr><td><strong>Tanggal Klaim</strong></td><td>: {{ \Carbon\Carbon::parse($klaim->tanggal_klaim)->format('d F Y') }}</td></tr>
                        <tr><td><strong>Kategori</strong></td><td>: {{ ucfirst($klaim->kategori) }}</td></tr>
                        <tr><td><strong>Nominal</strong></td><td>: <strong class="text-primary">Rp {{ number_format($klaim->nominal, 0, ',', '.') }}</strong></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><td width="150"><strong>Status</strong></td>
                            <td>:
                                @php
                                    $class = '';
                                    if($klaim->status == 'pending') $class = 'status-pending';
                                    elseif($klaim->status == 'approved') $class = 'status-approved';
                                    elseif($klaim->status == 'rejected') $class = 'status-rejected';
                                    else $class = 'status-paid';
                                @endphp
                                <span class="status-badge {{ $class }}">{{ ucfirst($klaim->status) }}</span>
                            </td>
                        </tr>
                        <tr><td><strong>Tanggal Pengajuan</strong></td><td>: {{ \Carbon\Carbon::parse($klaim->created_at)->format('d F Y H:i') }}</td></tr>
                        @if($klaim->approved_at)<tr><td><strong>Tanggal Disetujui</strong></td><td>: {{ \Carbon\Carbon::parse($klaim->approved_at)->format('d F Y H:i') }}</td></tr>@endif
                        @if($klaim->paid_at)<tr><td><strong>Tanggal Dibayar</strong></td><td>: {{ \Carbon\Carbon::parse($klaim->paid_at)->format('d F Y H:i') }}</td></tr>@endif
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <strong>Deskripsi:</strong>
                    <p class="mt-2">{{ $klaim->deskripsi }}</p>
                </div>
                @if($klaim->keterangan)
                <div class="col-12">
                    <strong>Keterangan:</strong>
                    <p class="mt-2">{{ $klaim->keterangan }}</p>
                </div>
                @endif
                @if($klaim->catatan_admin)
                <div class="col-12">
                    <strong class="text-danger">Catatan Admin:</strong>
                    <p class="mt-2 text-danger">{{ $klaim->catatan_admin }}</p>
                </div>
                @endif
                @if($klaim->bukti)
                <div class="col-12">
                    <strong>Bukti:</strong>
                    <br>
                    <a href="{{ asset($klaim->bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-2">
                        <i class="fas fa-download me-1"></i> Lihat Bukti
                    </a>
                </div>
                @endif
            </div>

            @if($klaim->status == 'pending')
            <hr>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">Tolak</button>
                <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#approveModal">Setujui</button>
            </div>
            @endif

            @if($klaim->status == 'approved')
            <hr>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#paidModal">Tandai Dibayar</button>
            </div>
            @endif
        </div>

        <!-- Modal Approve -->
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.klaim.approve', $klaim->id) }}" method="POST">
                    @csrf
                    <div class="modal-content rounded-4">
                        <div class="modal-header border-0">
                            <h5 class="fw-bold">Setujui Klaim</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Setujui klaim {{ $klaim->nomor_klaim }}?</p>
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
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.klaim.reject', $klaim->id) }}" method="POST">
                    @csrf
                    <div class="modal-content rounded-4">
                        <div class="modal-header border-0">
                            <h5 class="fw-bold">Tolak Klaim</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Tolak klaim {{ $klaim->nomor_klaim }}?</p>
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

        <!-- Modal Paid -->
        <div class="modal fade" id="paidModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.klaim.paid', $klaim->id) }}" method="POST">
                    @csrf
                    <div class="modal-content rounded-4">
                        <div class="modal-header border-0">
                            <h5 class="fw-bold">Tandai Dibayar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Klaim {{ $klaim->nomor_klaim }} sudah dibayar?</p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill">Ya, Sudah Dibayar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
