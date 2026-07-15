<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Karyawan - HR Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/data-karyawan.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

<div class="container-fluid p-lg-5 p-4">
    <!-- Header Section -->
    <div class="row mb-5 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-800 text-dark mb-2">Manajemen Sumber Daya</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active fw-semibold" aria-current="page text-primary">Karyawan</li>
                </ol>
            </nav>
        </div>
        <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
            <button class="btn btn-primary px-4 py-3 rounded-4 shadow-sm fw-bold border-0"
                    style="background: var(--primary);"
                    data-bs-toggle="modal" data-bs-target="#tambahKaryawanModal">
                <i class="fas fa-plus-circle me-2"></i>Rekrut Karyawan Baru
            </button>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card card h-100 border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-primary-soft text-primary me-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold text-uppercase">Total Tim</p>
                        <h3 class="fw-800 mb-0">{{ $karyawan->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card card h-100 border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-success-subtle text-success me-3">
                        <i class="fas fa-user-check fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-bold text-uppercase">Personel Aktif</p>
                        <h3 class="fw-800 mb-0 text-success">{{ $karyawan->where('status', 'Aktif')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="table-container">
        <!-- Table Toolbar -->
        <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="mb-0 fw-bold">Daftar Karyawan</h5>
            <div class="search-wrapper position-relative" style="min-width: 300px;">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" class="form-control ps-5 bg-light border-0" id="searchInput" placeholder="Cari nama, ID, atau divisi...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Karyawan</th>
                        <th>Divisi & Jabatan</th>
                        <th>Status</th>
                        <th>Tanggal Bergabung</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan as $k)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="position-relative">
                                    <img src="{{ $k->foto ? asset('storage/'.$k->foto) : 'https://ui-avatars.com/api/?name='.urlencode($k->nama).'&background=random' }}"
                                         class="rounded-circle border" width="48" height="48" style="object-fit: cover;">
                                    <span class="position-absolute bottom-0 end-0 p-1 {{ $k->status == 'Aktif' ? 'bg-success' : 'bg-danger' }} border border-white rounded-circle"></span>
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark mb-0">{{ $k->nama }}</div>
                                    <div class="text-muted small">#{{ $k->id_karyawan }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $k->divisi }}</div>
                            <div class="text-muted small">{{ $k->jabatan ?? 'Staff' }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2 {{ $k->status == 'Aktif' ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                <i class="fas fa-circle me-1 small"></i> {{ $k->status }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark small"><i class="far fa-calendar-alt me-1 text-muted"></i> {{ \Carbon\Carbon::parse($k->tanggal_bergabung)->format('d M Y') }}</div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.karyawan.detail-karyawan', $k->id) }}" class="btn-action text-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.karyawan.edit-karyawan', $k->id) }}" class="btn-action text-warning" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.karyawan.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data {{ $k->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action text-danger" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/gray/data-analysis.svg" style="width: 200px;" class="mb-3">
                            <p class="text-muted">Tidak ada data karyawan yang ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($karyawan->hasPages())
        <div class="p-4 border-top">
            {{ $karyawan->links() }}
        </div>
        @endif
    </div>
</div>
        @if($karyawan->hasPages())
        <div class="p-3 border-top bg-light-subtle">
            {{ $karyawan->links() }}
        </div>
        @endif
    </div>
</div>

    <!-- Modal Tambah Karyawan -->
    <div class="modal fade" id="tambahKaryawanModal" tabindex="-1" aria-labelledby="tambahKaryawanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 px-4">
                    <div>
                        <h5 class="modal-title fw-bold" id="tambahKaryawanModalLabel" style="font-family: 'Plus Jakarta Sans';">Tambah Karyawan Baru</h5>
                        <p class="text-muted small mb-0">Isi data karyawan dengan lengkap dan akurat.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data" id="formTambahKaryawan">
                    @csrf
                    <div class="modal-body px-4 pb-3">
                        <div class="row g-3">
                            <!-- ID Karyawan -->
                            <div class="col-md-6">
                                <label class="form-label">ID KARYAWAN <span class="text-danger">*</span></label>
                                <input type="text" name="id_karyawan" class="form-control @error('id_karyawan') is-invalid @enderror" placeholder="Contoh: EMP001" value="{{ old('id_karyawan') }}" required>
                                <small class="text-muted">ID unik untuk karyawan</small>
                                @error('id_karyawan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label class="form-label">NAMA LENGKAP <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Nama lengkap karyawan" value="{{ old('nama') }}" required>
                                @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label">EMAIL <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@perusahaan.com" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Telepon -->
                            <div class="col-md-6">
                                <label class="form-label">TELEPON</label>
                                <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" placeholder="Nomor telepon aktif" value="{{ old('telepon') }}">
                                @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Divisi -->
                            <div class="col-md-6">
                                <label class="form-label">DIVISI <span class="text-danger">*</span></label>
                                <select name="divisi" class="form-select @error('divisi') is-invalid @enderror" required>
                                    <option value="">Pilih Divisi</option>
                                    <option value="Teknologi Informasi" {{ old('divisi') == 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi</option>
                                    <option value="Sumber Daya Manusia" {{ old('divisi') == 'Sumber Daya Manusia' ? 'selected' : '' }}>Sumber Daya Manusia</option>
                                    <option value="Keuangan" {{ old('divisi') == 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                                    <option value="Pemasaran" {{ old('divisi') == 'Pemasaran' ? 'selected' : '' }}>Pemasaran</option>
                                    <option value="Operasional" {{ old('divisi') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                                    <option value="Penjualan" {{ old('divisi') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                                    <option value="Customer Service" {{ old('divisi') == 'Customer Service' ? 'selected' : '' }}>Customer Service</option>
                                </select>
                                @error('divisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-6">
                                <label class="form-label">JABATAN <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" placeholder="Contoh: Manager, Staff, Supervisor" value="{{ old('jabatan') }}" required>
                                @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Lulusan -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendidikan Terakhir</label>
                                <select name="lulusan" class="form-select @error('lulusan') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Lulusan</option>
                                    <option value="SMK">SMK</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                            </select>
                            @error('lulusan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">STATUS <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Non-Aktif" {{ old('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                    <option value="Cuti" {{ old('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tanggal Bergabung -->
                            <div class="col-md-6">
                                <label class="form-label">TANGGAL BERGABUNG <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_bergabung" class="form-control @error('tanggal_bergabung') is-invalid @enderror" value="{{ old('tanggal_bergabung') }}" required>
                                @error('tanggal_bergabung')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="col-12">
                                <label class="form-label">ALAMAT</label>
                                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" placeholder="Alamat lengkap karyawan">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Foto -->
                            <div class="col-12">
                                <label class="form-label">FOTO KARYAWAN</label>
                                <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                                @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom px-4 py-2 rounded-3" style="background: var(--primary);">
                            <i class="fas fa-save me-2"></i>Simpan Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <p>Apakah Anda yakin ingin menghapus karyawan <strong id="deleteEmployeeName"></strong>?</p>
                        <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/data-karyawan.js') }}"></script>
   </body>

</html>
