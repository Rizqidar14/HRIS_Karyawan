<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Klaim | Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/riwayat-klaim.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <div class="container-wrapper">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">
                    <i class="fas fa-history text-primary me-2"></i>
                    Riwayat Klaim Karyawan
                </h3>
                <a href="{{ route('admin.klaim.index') }}" class="btn btn-primary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Klaim Karyawan
                </a>
            </div>

            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-2">
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
                    <a href="{{ route('admin.klaim.riwayat') }}" class="btn btn-secondary rounded-pill">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>No. Klaim</th>
                            <th>Karyawan</th>
                            <th>Divisi</th>
                            <th>Tanggal Klaim</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tgl Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $row)
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
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    Belum ada data klaim
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $riwayat->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
