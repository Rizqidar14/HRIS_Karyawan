<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buat Akun - HRIS Plus</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/buat-akun.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>Registrasi Akun</h1>
                <p>Silakan lengkapi data akses di bawah ini.</p>
            </div>
            <a href="{{ route('admin.buat-akun.daftar') }}" class="btn-base btn-cancel" style="font-size: 12px;">
                <i class="fas fa-list me-1"></i> Daftar Akun
            </a>
        </div>

        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="tab-wrapper">
            <button class="tab-btn {{ (!session('active_tab') || session('active_tab') == 'karyawan') ? 'active' : '' }}" onclick="switchTab('karyawan')" id="tabKaryawan">Akun Karyawan</button>
            <button class="tab-btn {{ session('active_tab') == 'admin' ? 'active' : '' }}" onclick="switchTab('admin')" id="tabAdmin">Akun Admin</button>
        </div>

        <!-- FORM KARYAWAN -->
        <div class="form-card {{ (session('active_tab') && session('active_tab') != 'karyawan') ? 'hidden' : '' }}" id="formKaryawan">
            <div class="card-header-custom">
                <h2><i class="fas fa-user-circle me-2"></i>Data Akun Karyawan</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.buat-akun.store') }}" id="createAccountForm">
                    @csrf
                    <div class="form-grid">
                        <div class="full-width">
                            <label class="form-label">Nama Karyawan</label>
                            <div class="input-box">
                                <i class="fas fa-user-tie"></i>
                                <select class="form-select" id="karyawan_id" name="karyawan_id" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($karyawanWithoutAccount as $k)
                                    <option value="{{ $k->id }}" data-nama="{{ $k->nama }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Username</label>
                            <div class="input-box">
                                <i class="fas fa-at"></i>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Auto-generate" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Password</label>
                            <div class="input-box">
                                <i class="fas fa-key"></i>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Min. 3 karakter" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-box">
                                <i class="fas fa-key"></i>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        <div class="full-width">
                            <div class="role-notice">
                                <i class="fas fa-info-circle"></i>
                                <span>Role otomatis disetel sebagai <strong>Karyawan</strong>.</span>
                            </div>
                            <input type="hidden" name="role" value="karyawan">
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="reset" class="btn-base btn-cancel">Reset</button>
                        <button type="submit" class="btn-base btn-save">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- FORM ADMIN -->
        <div class="form-card {{ (session('active_tab') && session('active_tab') == 'admin') ? '' : 'hidden' }}" id="formAdmin">
            <div class="card-header-custom">
                <h2><i class="fas fa-shield-alt me-2"></i>Data Akun Administrator</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.buat-akun.store') }}" id="createAdminForm">
                    @csrf
                    <div class="form-grid">
                        <div class="full-width">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-box">
                                <i class="fas fa-user"></i>
                                <input type="text" class="form-control" name="nama" placeholder="Nama lengkap admin" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Username</label>
                            <div class="input-box">
                                <i class="fas fa-user-tag"></i>
                                <input type="text" class="form-control" name="username" placeholder="Username login" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Password</label>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" name="password" placeholder="Min. 3 karakter" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        <div class="full-width">
                            <label class="form-label">Role</label>
                            <div class="input-box">
                                <i class="fas fa-user-shield"></i>
                                <select class="form-select" name="role" required>
                                    <option value="admin">Administrator</option>
                                    <option value="karyawan">Karyawan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="reset" class="btn-base btn-cancel">Reset</button>
                        <button type="submit" class="btn-base btn-save">Daftarkan Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/buat-akun.js') }}"></script>
</body>

</html>
