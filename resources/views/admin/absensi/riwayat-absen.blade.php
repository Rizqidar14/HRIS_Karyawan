<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi - HRIS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/riwayat-absen.css') }}">

</head>

<body>
    @include('partials.navbar-admin')

    <div class="main-content">
        <div class="header-card">
            <div class="header-icon"><i class="fas fa-history"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">Riwayat Absensi</h4>
                <p class="text-muted small mb-0">Monitoring log kehadiran seluruh karyawan harian.</p>
            </div>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('admin.absensi.riwayat-absen') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label text-uppercase small">Nama Karyawan</label>
                        <input type="text" name="nama" class="form-control" placeholder="Cari nama..." value="{{ request('nama') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-uppercase small">Bulan</label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua</option>
                            @foreach($bulan as $key => $value)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label text-uppercase small">Tahun</label>
                        <select name="tahun" class="form-select">
                            @foreach($tahun as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label text-uppercase small">Tanggal Spesifik</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search me-1"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h6 class="mb-0 fw-bold">Log Kehadiran</h6>
                <span class="badge bg-light text-primary border rounded-pill px-3 py-2">
                    Total: {{ $absensi->total() }} Data
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center px-3" style="width: 50px;">No</th>
                            <th>Karyawan</th>
                            <th>Tanggal</th>
                            <th>Waktu (In/Out)</th>
                            <th>Durasi Kerja</th>
                            <th>Catatan Lembur</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $index => $a)
                        <tr>
                            <td class="text-center text-muted">{{ $absensi->firstItem() + $index }}</td>
                            <td><span class="fw-bold">{{ $a->nama }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($a->tanggal_masuk)->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="text-success small fw-600"><i class="fas fa-arrow-down me-1"></i> {{ $a->jam_masuk }}</div>
                                <div class="text-danger small fw-600"><i class="fas fa-arrow-up me-1"></i> {{ $a->jam_keluar ?? '--:--' }}</div>
                            </td>
                            <td class="fw-bold text-primary">{{ $a->total_jam ?? '-' }}</td>
                            <td style="max-width: 200px; white-space: normal;">
                                @if($a->lembur)
                                    <span class="small text-muted"><i class="fas fa-moon me-1 text-warning"></i> {{ $a->lembur }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-{{ strtolower($a->status) }}">
                                    {{ $a->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.absensi.hapus', $a->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted italic">Data riwayat tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top d-flex justify-content-center justify-content-md-end">
                {{ $absensi->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    </body>
</html>
