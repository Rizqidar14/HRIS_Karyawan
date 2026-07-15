function formatRupiah(angka) {
    if (isNaN(angka) || angka === 0) return "0";
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function cleanNumber(value) {
    if (!value) return 0;
    return parseInt(value.toString().replace(/[^0-9]/g, '')) || 0;
}

function getTerRate(bruto) {
    if (bruto <= 5400000) return 0;
    if (bruto <= 5650000) return 0.0025;
    if (bruto <= 6200000) return 0.005;
    if (bruto <= 6950000) return 0.0075;
    if (bruto <= 7100000) return 0.01;
    if (bruto <= 7390000) return 0.0125;
    if (bruto <= 7500000) return 0.015;
    if (bruto <= 8800000) return 0.0175;
    if (bruto <= 9650000) return 0.02;
    if (bruto <= 10410000) return 0.0225;
    return 0.025;
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function calculateAll() {
    let grandTotal = 0;
    document.querySelectorAll('tbody tr').forEach(row => {
        if (!row.querySelector('.input-gapok')) return;

        const gapok = cleanNumber(row.querySelector('.input-gapok').value);
        const tunjangan = cleanNumber(row.querySelector('.input-tunjangan').value);
        const lembur = cleanNumber(row.querySelector('.input-lembur').value);
        const bruto = gapok + tunjangan + lembur;

        // Hitung JHT (2% dari gaji pokok)
        const jhtValue = Math.round(gapok * 0.02);
        row.querySelector('.input-jht').value = formatRupiah(jhtValue);

        // Hitung PPh 21 berdasarkan TER
        const pphValue = Math.round(bruto * getTerRate(bruto));
        row.querySelector('.input-pph').value = formatRupiah(pphValue);

        // KONTRIBUSI BPJS KELAS 1 MANDIRI (Flat Rp 150.000)
        const bpjsValue = 150000;
        row.querySelector('.input-bpjs').value = formatRupiah(bpjsValue);

        // Ambil Nilai Potongan Cuti
        const cutiValue = cleanNumber(row.querySelector('.input-cuti').value);

        // Hitung Total Bersih (Netto)
        const totalNetto = bruto - (jhtValue + pphValue + bpjsValue + cutiValue);
        const nettoSpan = row.querySelector('.netto-value');
        if (nettoSpan) {
            nettoSpan.innerText = formatNumber(totalNetto);
        }

        grandTotal += totalNetto;
    });

    const totalElement = document.getElementById('total-pengeluaran-seluruh');
    if (totalElement) {
        totalElement.innerText = 'Rp ' + formatNumber(grandTotal);
    }
}

// Fungsi Pengubah Warna Dropdown Status Pembayaran Dinamis
function handleStatusStyles() {
    document.querySelectorAll('.payroll-status-select').forEach(select => {
        const applyColor = (el) => {
            if (el.value === 'sudah') {
                el.style.backgroundColor = '#d1fae5';
                el.style.color = '#059669';
                el.style.borderColor = '#6ee7b7';
            } else {
                el.style.backgroundColor = '#fee2e2';
                el.style.color = '#dc2626';
                el.style.borderColor = '#fca5a5';
            }
        };

        // Render warna awal sesuai data database
        applyColor(select);

        // Ubah warna real-time saat diganti oleh user admin
        select.addEventListener('change', function() {
            applyColor(this);
        });
    });
}

// Event listeners untuk deteksi input
document.querySelectorAll('.currency-input').forEach(input => {
    input.addEventListener('input', function() {
        let cleanVal = this.value.replace(/[^0-9]/g, '');
        this.value = formatRupiah(cleanVal);
        calculateAll();
    });

    input.addEventListener('blur', function() {
        if (this.value === '' || this.value === '0') {
            this.value = '0';
        }
    });
});

// Bersihkan format titik sebelum submit ke server database
document.getElementById('formPayroll')?.addEventListener('submit', function() {
    document.querySelectorAll('.currency-input').forEach(i => {
        i.value = i.value.replace(/\./g, '');
    });
});

window.onload = function() {
    calculateAll();
    handleStatusStyles(); // Panggil fungsi di sini!
};
