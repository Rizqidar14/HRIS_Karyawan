<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/riwayat-personal.css') }}">
</head>

<body>
    @include('partials.navbar-user')

    <div class="container py-5">
        <header class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold h3">Riwayat Presensi</h1>
                <p class="text-muted">Pantau kehadiran dan aktivitas lembur Anda.</p>
            </div>
            <a href="{{ route('user.absensi.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </header>

        <div class="bento-card mb-4">
            <form action="{{ route('user.absensi.riwayat') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="bulan" class="form-select border-0 bg-light rounded-3">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="tahun" class="form-select border-0 bg-light rounded-3">
                        <option value="">Semua Tahun</option>
                        @foreach($list_tahun as $th)
                        <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 rounded-3" style="background: var(--accent);">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="bento-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="text-muted small fw-bold">
                        <tr>
                            <th>TANGGAL</th>
                            <th>MASUK</th>
                            <th>KELUAR</th>
                            <th>STATUS</th>
                            <th>KETERANGAN</th>
                            <th>LEMBUR</th> </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr class="align-middle border-bottom">
                            <td class="py-3 fw-bold">{{ \Carbon\Carbon::parse($r->tanggal_masuk)->translatedFormat('d M Y') }}</td>
                            <td class="text-success fw-medium">{{ $r->jam_masuk ?? '--:--' }}</td>
                            <td class="text-danger fw-medium">{{ $r->jam_keluar ?? '--:--' }}</td>
                            <td>
                                <span class="status-badge bg-{{ $r->status }}">
                                    {{ strtoupper($r->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ $r->keterangan_status ? $r->keterangan_status : '-' }}
                            </td>
                            <td class="text-lembur">
                                {{ $r->lembur ? $r->lembur : '-' }} </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small italic">Tidak ada data riwayat pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $riwayat->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</body>

</html>
