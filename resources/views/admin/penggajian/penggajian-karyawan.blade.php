<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Payroll | HRIS System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/penggajian-karyawan.css') }}">
</head>
<body>

    @include('partials.navbar-admin')

    <main class="dashboard-container">
        <div class="alert alert-warning alert-info-payroll d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-circle me-3 fa-lg flex-shrink-0"></i>
            <div>
                <strong>Informasi Penting:</strong> Seluruh pengisian dan rincian payroll wajib diselesaikan serta dibayarkan paling lambat setiap <strong>tanggal 25</strong>.
            </div>
        </div>

        <div class="hero-welcome">
            <div>
                <h2><i class="fas fa-money-bill-wave me-2 text-primary"></i>Manajemen Payroll</h2>
                <p class="text-secondary small mb-0">
                    <i class="fas fa-calendar-alt me-1"></i>Periode <strong>{{ $daftarBulan[$bulan] }} {{ $tahun }}</strong>
                </p>
            </div>
            <div class="total-budget-card">
                <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                    <i class="fas fa-chart-line me-1"></i>Total Pengeluaran Gaji
                </div>
                <div class="h5 mb-0 fw-bold text-primary" id="total-pengeluaran-seluruh">Rp 0</div>
            </div>
        </div>

        <div class="content-card">
            <div class="filter-section">
                <form method="GET" action="{{ route('admin.penggajian.index') }}" class="d-flex gap-3 flex-wrap w-100">
                    <div class="filter-group">
                        <label><i class="fas fa-calendar me-1"></i>Bulan</label>
                        <select name="bulan" class="form-select form-select-sm" style="width: 180px;">
                            @foreach($daftarBulan as $key => $namaBulan)
                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                    {{ $namaBulan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-calendar-week me-1"></i>Tahun</label>
                        <select name="tahun" class="form-select form-select-sm" style="width: 120px;">
                            @foreach($daftarTahun as $thn)
                                <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>
                                    {{ $thn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn-payroll btn-payroll-primary">
                            <i class="fas fa-filter me-1"></i>Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <form id="formPayroll" action="{{ route('admin.penggajian.update') }}" method="POST">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            <div class="content-card">
                <div class="card-header-payroll d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h6 class="mb-0 fw-bold text-slate-800">
                        <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Rincian Gaji Karyawan
                        <span class="badge bg-secondary ms-2">{{ $data_gaji->count() }} Karyawan</span>
                    </h6>

                    <div class="d-flex align-items-center gap-2">
                        @if(!$data_gaji->isEmpty())
                            <div class="btn-group" role="group">
                                <button type="button" class="btn-payroll btn-payroll-success" id="btn-set-sudah-semua" title="Ubah semua status menjadi Sudah Dibayar">
                                    <i class="fas fa-check-double me-1"></i> Set Sudah Dibayar
                                </button>
                                <button type="button" class="btn-payroll btn-payroll-secondary" id="btn-set-belum-semua" title="Kembalikan semua status menjadi Belum Dibayar">
                                    <i class="fas fa-undo me-1"></i> Set Belum
                                </button>
                            </div>
                        @endif

                        <button type="submit" class="btn-payroll btn-payroll-primary fw-bold">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 13%">KARYAWAN</th>
                                <th style="width: 10%">GAJI POKOK <span class="subtitle">Basic Salary</span></th>
                                <th style="width: 10%">TUNJANGAN <span class="subtitle">Allowance</span></th>
                                <th style="width: 10%">LEMBUR <span class="subtitle">Rp 50.000/Jam</span></th>
                                <th style="width: 7%">TOTAL JAM <span class="subtitle">Hours</span></th>
                                <th style="width: 8%">JHT (2%) <span class="potongan-badge">Potongan</span></th>
                                <th style="width: 8%">PPH 21 <span class="potongan-badge">Potongan</span></th>
                                <th style="width: 8%">BPJS (1%) <span class="potongan-badge">Potongan</span></th>
                                <th style="width: 8%">POT. CUTI <span class="potongan-badge">Potongan</span></th>
                                <th style="width: 10%">TOTAL NETTO <span class="subtitle">Take Home Pay</span></th>
                                <th style="width: 8%">STATUS <span class="subtitle">Payment</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data_gaji as $gaji)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $gaji->nama }}</div>
                                    <div class="small text-muted" style="font-size: 0.7rem;">
                                        <i class="fas fa-briefcase me-1"></i>{{ $gaji->jabatan }}
                                        <span class="mx-1">|</span>
                                        <i class="fas fa-graduation-cap me-1"></i>{{ $gaji->lulusan }}
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="gaji[{{ $gaji->id_karyawan }}][gaji_pokok]"
                                               class="form-control form-control-payroll text-end currency-input input-gapok"
                                               value="{{ number_format($gaji->gaji_pokok, 0, ',', '.') }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="gaji[{{ $gaji->id_karyawan }}][tunjangan]"
                                               class="form-control form-control-payroll text-end currency-input input-tunjangan"
                                               value="{{ number_format($gaji->tunjangan, 0, ',', '.') }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-success bg-opacity-10 text-success">+</span>
                                        <input type="text" name="gaji[{{ $gaji->id_karyawan }}][lembur]"
                                            class="form-control form-control-payroll text-end currency-input input-lembur"
                                            value="{{ number_format($gaji->nominal_lembur, 0, ',', '.') }}">
                                    </div>
                                </td>
                                <td>
                                    <span class="jam-lembur-badge">
                                        <i class="fas fa-clock"></i>
                                        {{ number_format($gaji->total_jam_lembur, 0) }} Jam
                                    </span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-danger bg-opacity-10 text-danger">-</span>
                                        <input type="text" name="gaji[{{ $gaji->id_karyawan }}][jht]"
                                               class="form-control form-control-payroll text-end text-danger fw-bold currency-input input-jht"
                                               value="{{ number_format($gaji->jht, 0, ',', '.') }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-danger bg-opacity-10 text-danger">-</span>
                                        <input type="text" name="gaji[{{ $gaji->id_karyawan }}][pph21]"
                                               class="form-control form-control-payroll text-end text-danger fw-bold currency-input input-pph"
                                               value="{{ number_format($gaji->pph21, 0, ',', '.') }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-danger bg-opacity-10 text-danger">-</span>
                                        <input type="text" name="gaji[{{ $gaji->id_karyawan }}][bpjs]"
                                               class="form-control form-control-payroll text-end text-danger fw-bold currency-input input-bpjs"
                                               value="{{ number_format($gaji->bpjs, 0, ',', '.') }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-danger bg-opacity-10 text-danger">-</span>
                                        <input type="text" name="gaji[{{ $gaji->id_karyawan }}][pot_cuti]"
                                               class="form-control form-control-payroll text-end text-danger fw-bold currency-input input-cuti"
                                               value="{{ number_format($gaji->pot_cuti, 0, ',', '.') }}">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="total-netto-badge">
                                        <i class="fas fa-hand-holding-usd me-1"></i>
                                        Rp <span class="netto-value">0</span>
                                    </span>
                                </td>
                                <td>
                                    <select name="gaji[{{ $gaji->id_karyawan }}][status]"
                                            class="form-select form-select-sm payroll-status-select text-center fw-bold">
                                        <option value="belum" {{ ($gaji->status ?? 'belum') == 'belum' || ($gaji->status ?? 'belum') == 'pending' ? 'selected' : '' }}>❌ Belum</option>
                                        <option value="sudah" {{ ($gaji->status ?? 'belum') == 'sudah' ? 'selected' : '' }}>✅ Sudah</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach

                            @if($data_gaji->isEmpty())
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <i class="fas fa-database fa-2x text-muted mb-2 d-block"></i>
                                    <p class="text-muted mb-0">Tidak ada data karyawan</p>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </main>

    <script src="{{ asset('js/penggajian-karyawan.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnSetSudah = document.getElementById('btn-set-sudah-semua');
            const btnSetBelum = document.getElementById('btn-set-belum-semua');
            const statusSelects = document.querySelectorAll('.payroll-status-select');

            if (btnSetSudah) {
                btnSetSudah.addEventListener('click', function () {
                    statusSelects.forEach(select => {
                        select.value = 'sudah';
                    });
                });
            }

            if (btnSetBelum) {
                btnSetBelum.addEventListener('click', function () {
                    statusSelects.forEach(select => {
                        select.value = 'belum';
                    });
                });
            }
        });
    </script>
</body>
</html>
