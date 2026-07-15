<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - HRIS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/detail-akun.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <h1>Daftar Akun Sistem</h1>
                <p class="text-muted">Kelola kredensial dan hak akses pengguna HRIS</p>
            </div>
            <a href="{{ route('admin.buat-akun.form') }}" class="btn-create">
                <i class="fas fa-plus-circle"></i> Tambah Akun Baru
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3 class="mb-0">{{ $totalAkun ?? 0 }}</h3>
                    <p class="mb-0">Total Pengguna</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                <div class="stat-info">
                    <h3 class="mb-0">{{ $adminCount ?? 0 }}</h3>
                    <p class="mb-0">Administrator</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                <div class="stat-info">
                    <h3 class="mb-0">{{ $karyawanCount ?? 0 }}</h3>
                    <p class="mb-0">Karyawan</p>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-top">
                <h5 class="mb-0 fw-bold">Data Akun Terdaftar</h5>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau username...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Info Pengguna</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        @forelse($users ?? [] as $index => $user)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $user->nama_display ?? $user->nama_karyawan ?? '-' }}</div>
                                @if($user->role == 'karyawan' && isset($user->nip))
                                <small class="text-muted">NIP: {{ $user->nip }}</small>
                                @endif
                            </td>
                            <td><code class="text-primary fw-medium">{{ $user->username ?? '-' }}</code></td>
                            <td>
                                <span class="badge-ui {{ $user->role == 'admin' ? 'badge-admin' : 'badge-karyawan' }}">
                                    <i class="fas {{ $user->role == 'admin' ? 'fa-user-shield' : 'fa-user-tie' }}"></i>
                                    {{ ucfirst($user->role ?? '-') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-ui {{ ($user->status ?? 'nonaktif') == 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                    <i class="fas fa-circle" style="font-size: 6px;"></i>
                                    {{ ucfirst($user->status ?? 'nonaktif') }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon btn-view" onclick="viewUser({{ $user->id }})" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button class="btn-icon info btn-hash" onclick="viewPasswordHash({{ $user->id }})" title="Hash Password">
                                        <i class="fas fa-key"></i>
                                    </button>

                                    <button class="btn-icon warning btn-edit" onclick="openEditModal({{ $user->id }})" title="Edit Akun & Password">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn-icon danger btn-delete"
                                        onclick="deleteUser(this)"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ addslashes($user->nama_display ?? $user->nama_karyawan ?? '') }}"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">Belum ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Data Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" id="edit_username" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <select id="edit_role" class="form-select">
                                    <option value="admin">Admin</option>
                                    <option value="karyawan">Karyawan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select id="edit_status" class="form-select">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ganti Password (Kosongkan jika tidak diubah)</label>
                            <div class="input-group">
                                <input type="password" id="edit_password" class="form-control" placeholder="Password baru...">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleEditPass()">
                                    <i class="fas fa-eye" id="eyeIconEdit"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-key me-2"></i>Hash Password System</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-2">String terenkripsi di database:</p>
                    <div class="bg-light p-3 border rounded">
                        <code id="passwordHashContent" class="word-break-all text-dark"></code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-circle me-2"></i>Informasi Detail Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="userModalBody"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/detail-akun.js') }}"></script>

</body>

</html>
