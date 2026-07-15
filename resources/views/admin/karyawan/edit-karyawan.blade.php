<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil Karyawan - HR Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/edit-karyawan.css') }}">
</head>

<body>
    @include('partials.navbar-admin')

    <div class="main-wrapper">
        <div class="page-header">
            <div>
                <h1 class="page-title">Edit Profil Karyawan</h1>
                <p class="text-muted m-0">Memperbarui data personel untuk ID: <b>#{{ $karyawan->id_karyawan }}</b></p>
            </div>
            <a href="{{ route('admin.karyawan.data-karyawan') }}" class="btn-cancel-soft">
                <i class="fas fa-times me-2"></i> Batal
            </a>
        </div>

        <div class="edit-card">
            <form action="{{ route('admin.karyawan.update', $karyawan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-inner">
                    <div class="row g-5">
                        <div class="col-lg-4">
                            <div class="photo-upload-zone">
                                <div id="image-preview-container">
                                    @if($karyawan->foto)
                                    <img src="{{ asset('storage/' . $karyawan->foto) }}" class="img-preview-lg" id="preview-image">
                                    @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($karyawan->nama) }}&size=200&background=4361ee&color=fff" class="img-preview-lg" id="preview-image">
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-2">Foto Profil</h6>
                                <p class="text-muted small mb-4">Pastikan wajah terlihat jelas dengan pencahayaan yang cukup.</p>

                                <input type="file" name="foto" id="file-upload" hidden accept="image/*">
                                <button type="button" class="btn btn-outline-primary fw-bold rounded-pill px-4" onclick="document.getElementById('file-upload').click()">
                                    <i class="fas fa-camera me-2"></i> Ubah Foto
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="form-section-label">
                                <div class="section-icon"><i class="fas fa-user"></i></div>
                                <span class="section-text">Informasi Identitas</span>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" value="{{ $karyawan->nama }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Institusi</label>
                                    <input type="email" name="email" class="form-control" value="{{ $karyawan->email }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" name="telepon" class="form-control" value="{{ $karyawan->telepon }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alamat Domisili</label>
                                    <input type="text" name="alamat" class="form-control" value="{{ $karyawan->alamat }}">
                                </div>
                            </div>

                            <div class="form-section-label">
                                <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                                <span class="section-text">Penempatan Kerja</span>
                            </div>

                            <div class="row g-3 mb-5">
                                <div class="col-md-4">
                                    <label class="form-label">Divisi</label>
                                    <select name="divisi" class="form-select">
                                        @foreach(['IT', 'HRD', 'Finance', 'Marketing', 'Operational'] as $div)
                                        <option value="{{ $div }}" {{ $karyawan->divisi == $div ? 'selected' : '' }}>{{ $div }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control" value="{{ $karyawan->jabatan }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Pendidikan Terakhir</label>
                                    <select name="lulusan" class="form-select @error('lulusan') is-invalid @enderror" required>
                                        <option value="SMK" {{ $karyawan->lulusan == 'SMK' ? 'selected' : '' }}>SMK</option>
                                        <option value="S1" {{ $karyawan->lulusan == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ $karyawan->lulusan == 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ $karyawan->lulusan == 'S3' ? 'selected' : '' }}>S3</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status Kepegawaian</label>
                                    <select name="status" class="form-select">
                                        <option value="Aktif" {{ $karyawan->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Cuti" {{ $karyawan->status == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                        <option value="Non-Aktif" {{ $karyawan->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Tanggal Mulai Kontrak</label>
                                    <input type="date" name="tanggal_bergabung" class="form-control" value="{{ \Carbon\Carbon::parse($karyawan->tanggal_bergabung)->format('Y-m-d') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                                <button type="reset" class="btn-cancel-soft">Reset Form</button>
                                <button type="submit" class="btn-save-premium">
                                    <i class="fas fa-check-circle me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time Photo Preview
        document.getElementById('file-upload').onchange = evt => {
            const [file] = evt.target.files
            if (file) {
                document.getElementById('preview-image').src = URL.createObjectURL(file)
            }
        }
    </script>
</body>

</html>
