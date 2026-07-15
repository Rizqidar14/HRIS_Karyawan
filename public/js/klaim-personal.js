 document.addEventListener('DOMContentLoaded', function() {
            // Format nominal rupiah live
            const nominalInput = document.getElementById('nominalInput');
            const totalDisplay = document.getElementById('totalDisplay');

            nominalInput.addEventListener('input', function() {
                let value = this.value;
                if (value === '') value = 0;
                let formatter = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                });
                totalDisplay.innerHTML = formatter.format(value);
            });

            // Loading state submit
            const form = document.getElementById('formKlaim');
            const btn = document.getElementById('btnSubmit');

            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengajukan...';
            });

            // Preview kategori
            const kategoriSelect = document.getElementById('kategoriSelect');
            kategoriSelect.addEventListener('change', function() {
                console.log('Kategori dipilih:', this.value);
            });
        });
