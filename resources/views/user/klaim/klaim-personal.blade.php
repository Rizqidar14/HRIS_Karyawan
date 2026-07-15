<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Klaim Reimbursement | Portal Karyawan</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/klaim-personal.css') }}">

</head>

<body>
    @include('partials.navbar-user')

    <div class="container-wrapper">
        <div class="row g-4">
            <!-- Kolom Kiri: Form Klaim -->
            <div class="col-lg-6">
                <div class="card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 fw-bold mb-0">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                            Form Klaim Reimbursement
                        </h3>
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            <i class="fas fa-receipt me-1"></i> {{ $nomorKlaim }}
                        </span>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-4 mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-4 mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('user.klaim.store') }}" method="POST" enctype="multipart/form-data" id="formKlaim">
                        @csrf
                        <input type="hidden" name="nomor_klaim" value="{{ $nomorKlaim }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-user text-muted me-1"></i> Nama Karyawan
                                </label>
                                <input type="text" class="form-control bg-light" value="{{ $userName }}" readonly disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-building text-muted me-1"></i> Divisi
                                </label>
                                <input type="text" class="form-control bg-light" value="{{ $divisi }}" readonly disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt text-muted me-1"></i> Tanggal Klaim <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal_klaim" class="form-control @error('tanggal_klaim') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                                @error('tanggal_klaim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-tags text-muted me-1"></i> Kategori <span class="text-danger">*</span>
                                </label>
                                <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required id="kategoriSelect">
                                    <option value="">Pilih Kategori</option>
                                    <option value="transportasi">🚗 Transportasi & Perjalanan</option>
                                    <option value="konsumsi">🍽️ Konsumsi & Makan</option>
                                    <option value="alat_tulis">✏️ Alat Tulis Kantor (ATK)</option>
                                    <option value="komunikasi">📱 Komunikasi & Internet</option>
                                    <option value="cetak">🖨️ Cetak & Dokumen</option>
                                    <option value="kesehatan">💊 Kesehatan & Kebersihan</option>
                                    <option value="training">📚 Training & Sertifikasi</option>
                                    <option value="entertainment">🎬 Entertainment & Tamu</option>
                                    <option value="lainnya">📦 Lainnya</option>
                                </select>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left text-muted me-1"></i> Deskripsi <span class="text-danger">*</span>
                            </label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" placeholder="Contoh: Pembelian tiket kereta api Jakarta-Surabaya untuk tugas dinas" required></textarea>
                            <small class="text-muted">Jelaskan secara detail keperluan klaim Anda</small>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-money-bill-wave text-muted me-1"></i> Nominal (Rp) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3">Rp</span>
                                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" placeholder="0" min="1000" step="1000" required id="nominalInput">
                                </div>
                                @error('nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-paperclip text-muted me-1"></i> Bukti / Struk
                                </label>
                                <input type="file" name="bukti" class="form-control @error('bukti') is-invalid @enderror" accept="image/*,.pdf">
                                <small class="text-muted">Format: JPG, PNG, PDF (Max 2MB)</small>
                                @error('bukti')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-sticky-note text-muted me-1"></i> Keterangan Tambahan
                            </label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Informasi tambahan jika diperlukan..."></textarea>
                        </div>

                        <hr>

                        <div class="info-card mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="opacity-75">Total Klaim</small>
                                    <h4 class="mb-0 fw-bold" id="totalDisplay">Rp 0</h4>
                                </div>
                                <i class="fas fa-receipt fa-2x opacity-50"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i>
                            Ajukan Klaim Reimbursement
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Informasi & Panduan -->
            <div class="col-lg-6">
                <!-- Informasi Panduan -->
                <div class="card-custom p-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Panduan Klaim Reimbursement
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-check-circle text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Syarat Klaim</h6>
                                    <small class="text-muted">Struk/bukti transaksi asli<br>Deskripsi jelas dan lengkap<br>Maksimal 7 hari setelah transaksi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Proses Klaim</h6>
                                    <small class="text-muted">Proses approval 1-3 hari kerja<br>Pencairan H+7 setelah approved</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Klaim Terbaru -->
                <div class="card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-history text-primary me-2"></i>
                            Riwayat Klaim Terbaru
                        </h5>
                        <a href="{{ route('user.klaim.riwayat') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No. Klaim</th>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatKlaim as $row)
                                    <tr>
                                        <td><small>{{ $row->nomor_klaim }}</small></td>
                                        <td><small>{{ \Carbon\Carbon::parse($row->tanggal_klaim)->format('d/m/Y') }}</small></td>
                                        <td>
                                            <small>
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
                                            </small>
                                        </td>
                                        <td><small class="fw-bold">Rp {{ number_format($row->nominal, 0, ',', '.') }}</small></td>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Belum ada riwayat klaim
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

    <script src="{{ asset('js/klaim-personal.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
