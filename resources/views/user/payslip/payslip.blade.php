<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ session('user')['nama_karyawan'] }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/payslip.css') }}">
</head>
<body>
    @include('partials.navbar-user')

    <div class="container">
        <div class="payslip-card">
            @if($gaji)
            <!-- Header -->
            <div class="payslip-header">
                <div class="brand-section">
                    <h2>SLIP GAJI KARYAWAN</h2>
                    <p>PT. RIZQI COMPUTER</p>
                </div>
                <div class="status-section">
                    <div class="status-badge">Terbayar</div>
                    <p style="margin: 8px 0 0; font-size: 0.8rem; color: var(--slate-500);">
                        ID Transaksi: #PAY-{{ $gaji->id }}-{{ date('Y') }}
                    </p>
                </div>
            </div>

            <!-- Meta Data -->
            <div class="meta-grid">
                <div class="meta-item">
                    <label>Nama Karyawan</label>
                    <span>{{ session('user')['nama_karyawan'] }}</span>
                </div>
                <div class="meta-item">
                    <label>Periode Gaji</label>
                    <span>{{ $gaji->periode_bulan }}</span>
                </div>
                <div class="meta-item">
                    <label>Tanggal Cetak</label>
                    <span>{{ date('d M Y') }}</span>
                </div>
            </div>

            <!-- Isi Slip -->
            <div class="payslip-body">
                <div class="financial-grid">
                    <!-- Earnings -->
                    <div>
                        <div class="section-title">
                            <span>Pendapatan</span>
                            <i class="fa-solid fa-arrow-trend-up text-success"></i>
                        </div>
                        <div class="row-item">
                            <span class="label">Gaji Pokok</span>
                            <span class="value">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</span>
                        </div>
                        <div class="row-item">
                            <span class="label">Tunjangan Jabatan</span>
                            <span class="value">Rp {{ number_format($gaji->tunjangan, 0, ',', '.') }}</span>
                        </div>
                        <div class="row-item">
                            <span class="label">Lembur</span>
                            <span class="value">Rp {{ number_format($gaji->lembur, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div>
                        <div class="section-title">
                            <span>Potongan</span>
                            <i class="fa-solid fa-arrow-trend-down text-danger"></i>
                        </div>
                        <div class="row-item">
                            <span class="label">Cuti </span>
                            <span class="value minus">- Rp {{ number_format($gaji->pot_cuti, 0, ',', '.') }}</span>
                        </div>
                        <div class="row-item">
                            <span class="label">PPh21 (Pajak)</span>
                            <span class="value minus">- Rp {{ number_format($gaji->pph21, 0, ',', '.') }}</span>
                        </div>
                        <div class="row-item">
                            <span class="label">BPJS Kesehatan</span>
                            <span class="value minus">- Rp {{ number_format($gaji->bpjs, 0, ',', '.') }}</span>
                        </div>
                        <div class="row-item">
                            <span class="label">JHT (BPJS Ketenagakerjaan)</span>
                            <span class="value minus">- Rp {{ number_format($gaji->jht, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="summary-wrapper">
                    <div class="total-info">
                        <label>GAJI BERSIH YANG DITERIMA (NET)</label>
                        <div class="total-amount">Rp {{ number_format($gaji->total_diterima, 0, ',', '.') }}</div>
                    </div>
                    <button class="btn-print" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Cetak Dokumen
                    </button>
                </div>

                <div class="disclaimer">
                    <p>Dokumen ini diterbitkan secara otomatis oleh Sistem HRIS Plus dan sah tanpa tanda tangan basah.</p>
                </div>
            </div>
            @else
            <div style="padding: 100px 20px; text-align: center;">
                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 4rem; color: var(--slate-200); margin-bottom: 1.5rem;"></i>
                <h3 style="color: var(--slate-500);">Data gaji periode ini belum tersedia.</h3>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
