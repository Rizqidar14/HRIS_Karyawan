function switchTab(tab) {
            const fKaryawan = document.getElementById('formKaryawan');
            const fAdmin = document.getElementById('formAdmin');
            const tKaryawan = document.getElementById('tabKaryawan');
            const tAdmin = document.getElementById('tabAdmin');

            if (tab === 'karyawan') {
                fKaryawan.classList.remove('hidden');
                fAdmin.classList.add('hidden');
                tKaryawan.classList.add('active');
                tAdmin.classList.remove('active');
            } else {
                fAdmin.classList.remove('hidden');
                fKaryawan.classList.add('hidden');
                tAdmin.classList.add('active');
                tKaryawan.classList.remove('active');
            }

            // Simpan ke localStorage agar tab terakhir tetap teringat
            localStorage.setItem('activeTab', tab);

            // Kirim ke server untuk disimpan di session
            fetch('{{ route("admin.buat-akun.set-tab") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    tab: tab
                })
            }).catch(error => console.error('Error saving tab:', error));
        }

        // Saat halaman dimuat, cek localStorage untuk tab terakhir
        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('activeTab');
            if (savedTab && savedTab === 'admin') {
                switchTab('admin');
            }
        });

        // Auto-generate username saat pilih karyawan
        const karyawanSelect = document.getElementById('karyawan_id');
        if (karyawanSelect) {
            karyawanSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (option.value) {
                    const nama = option.getAttribute('data-nama');
                    // Generate username: namalowercase + random 2 digit angka
                    const username = nama.toLowerCase().replace(/\s+/g, '') + Math.floor(10 + Math.random() * 90);
                    document.getElementById('username').value = username;
                } else {
                    document.getElementById('username').value = '';
                }
            });
        }
