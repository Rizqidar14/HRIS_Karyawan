<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Persetujuan Cuti - HRIS Plus</title>

    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/cuti-karyawan.css') }}">
</head>

<body>
    <!-- Navbar (Identik dengan Dashboard[cite: 2]) -->
    @include('partials.navbar-admin')

    <!-- Container Utama (Identik dengan Dashboard[cite: 2]) -->
    <main class="dashboard-container">

        <!-- Header Ringkas (Style Dashboard[cite: 2]) -->
        <div class="hero-welcome">
            <div>
                <h2><i class="fa-solid fa-calendar-check text-primary me-2"></i> Persetujuan Cuti</h2>
                <p class="mb-0 opacity-75 small">Sistem memantau pengajuan cuti karyawan secara real-time.</p>
            </div>
            <div class="live-clock">
                <span class="year-text">{{ date('Y') }}</span>
                <span class="sub-text">Tahun Operasional</span>
            </div>
        </div>

        <!-- Filter Area (Menggunakan Content Card Dashboard[cite: 2]) -->
        <div class="content-card">
            <form action="{{ route('admin.cuti.index') }}" method="GET" class="filter-grid">
                <div>
                    <label class="form-label-custom">Cari Karyawan</label>
                    <input type="text" name="search" class="form-control-custom" placeholder="Nama..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="form-label-custom">Dari</label>
                    <input type="date" name="start_date" class="form-control-custom" value="{{ request('start_date') }}">
                </div>
                <div>
                    <label class="form-label-custom">Sampai</label>
                    <input type="date" name="end_date" class="form-control-custom" value="{{ request('end_date') }}">
                </div>
                <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 10px; padding: 10px 25px;">
                    <i class="fa-solid fa-filter me-2"></i> Filter
                </button>
            </form>
        </div>

        <!-- Tabel Data (Menggunakan Content Card Dashboard[cite: 2]) -->
        <div class="content-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Jenis</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th class="text-center">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataCuti as $cuti)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($cuti->nama, 0, 1)) }}
                                    </div>
                                    <div class="fw-bold" style="font-size: 0.85rem;">{{ $cuti->nama }}</div>
                                </div>
                            </td>
                            <td data-label="Jenis">{{ $cuti->jenis_cuti }}</td>
                            <td data-label="Durasi">{{ $cuti->jumlah_hari }} Hari</td>
                            <td data-label="Status">
                                @if($cuti->status == 'Menunggu')
                                    <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">PENDING</span>
                                @else
                                    <span class="badge bg-success" style="font-size: 0.6rem;">SELESAI</span>
                                @endif
                            </td>
                            <td align="center">
                                @if($cuti->status == 'Menunggu')
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="{{ route('admin.cuti.update-status', $cuti->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Disetujui">
                                            <button class="btn btn-sm btn-primary"><i class="fa-solid fa-check"></i></button>
                                        </form>
                                        <!-- Token & Fitur Tetap Terjaga[cite: 1] -->
                                    </div>
                                @else
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 opacity-50 small">Tidak ada data pengajuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
