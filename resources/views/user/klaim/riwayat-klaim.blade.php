<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Klaim Reimbursement | Portal Karyawan</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/riwayat-klaim.css') }}">
</head>
<body>

@include('partials.navbar-user')

<div class="container-wrapper">
    <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h3 class="h4 fw-bold mb-0">
                <i class="fas fa-history text-primary me-2"></i>
                Riwayat Klaim Reimbursement
            </h3>
            <a href="{{ route('user.klaim.index') }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus me-2"></i> Buat Klaim Baru
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select rounded-pill">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="bulan" class="form-select rounded-pill">
                    <option value="">Semua Bulan</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="tahun" class="form-select rounded-pill">
                    <option value="">Semua Tahun</option>
                    @for($i=2023; $i<=2026; $i++)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                <a href="{{ route('user.klaim.riwayat') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="fas fa-undo me-1"></i> Reset
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Klaim</th>
                        <th>Tanggal Klaim</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $row)
                        <tr>
                            <td><span class="fw-bold">{{ $row->nomor_klaim }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal_klaim)->format('d/m/Y') }}</td>
                            <td>
                                @switch($row->kategori)
                                    @case('transportasi') 🚗 Transportasi @break
                                    @case('konsumsi') 🍽️ Konsumsi @break
                                    @case('alat_tulis') ✏️ ATK @break
                                    @case('komunikasi') 📱 Komunikasi @break
                                    @case('cetak') 🖨️ Cetak @break
                                    @case('kesehatan') 💊 Kesehatan @break
                                    @case('training') 📚 Training @break
                                    @case('entertainment') 🎬 Entertainment @break
                                    @default 📦 Lainnya
                                @endswitch
                            </td>
                            <td>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($row->deskripsi, 50) }}</small>
                            </td>
                            <td class="fw-bold text-primary">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusClass = '';
                                    $statusText = ucfirst($row->status);
                                    if($row->status == 'pending') $statusClass = 'status-pending';
                                    elseif($row->status == 'approved') $statusClass = 'status-approved';
                                    elseif($row->status == 'rejected') $statusClass = 'status-rejected';
                                    elseif($row->status == 'paid') $statusClass = 'status-paid';
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td><small>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</small></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info rounded-circle" data-bs-toggle="modal" data-bs-target="#detailModal{{ $row->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="detailModal{{ $row->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4">
                                    <div class="modal-header border-0">
                                        <h5 class="fw-bold">Detail Klaim</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <small class="text-muted text-uppercase">No. Klaim</small>
                                            <p class="fw-bold">{{ $row->nomor_klaim }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted text-uppercase">Deskripsi Lengkap</small>
                                            <p class="mb-0">{{ $row->deskripsi }}</p>
                                        </div>
                                        @if($row->keterangan)
                                        <div class="mb-3">
                                            <small class="text-muted text-uppercase">Keterangan</small>
                                            <p class="mb-0">{{ $row->keterangan }}</p>
                                        </div>
                                        @endif
                                        @if($row->catatan_admin)
                                        <div class="mb-3">
                                            <small class="text-muted text-uppercase">Catatan Admin</small>
                                            <p class="mb-0 text-danger">{{ $row->catatan_admin }}</p>
                                        </div>
                                        @endif
                                        @if($row->bukti)
                                        <div class="mb-3">
                                            <small class="text-muted text-uppercase">Bukti Transaksi</small>
                                            <br>
                                            <a href="{{ asset($row->bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-1">
                                                <i class="fas fa-download me-1"></i> Lihat Bukti
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Belum ada data klaim reimbursement
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-4">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
