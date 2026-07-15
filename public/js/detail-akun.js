 // Fitur Cari
        document.getElementById('searchInput')?.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('#userTableBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });

        // 1. LIHAT HASH (Fungsi Kunci)
        function viewPasswordHash(id) {
            const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
            document.getElementById('passwordHashContent').innerText = 'Memuat...';
            modal.show();
            fetch(`/admin/akun/user-detail-with-password/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) document.getElementById('passwordHashContent').innerText = res.data.password_full_hash;
                });
        }

        // 2. MODAL EDIT & DATA FETCHING
        function openEditModal(id) {
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            document.getElementById('editUserForm').reset();

            fetch(`/admin/akun/user-detail/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const u = res.data;
                        document.getElementById('edit_id').value = u.id;
                        document.getElementById('edit_nama').value = u.nama_display || u.nama_karyawan;
                        document.getElementById('edit_username').value = u.username;
                        document.getElementById('edit_role').value = u.role;
                        document.getElementById('edit_status').value = u.status || 'aktif';
                        modal.show();
                    }
                });
        }

        // Toggle Password Visibility
        function toggleEditPass() {
            const passInput = document.getElementById('edit_password');
            const eyeIcon = document.getElementById('eyeIconEdit');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.className = 'fas fa-eye-slash';
            } else {
                passInput.type = 'password';
                eyeIcon.className = 'fas fa-eye';
            }
        }

        // Submit Update via AJAX
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const payload = {
                nama: document.getElementById('edit_nama').value,
                username: document.getElementById('edit_username').value,
                role: document.getElementById('edit_role').value,
                status: document.getElementById('edit_status').value,
                password: document.getElementById('edit_password').value
            };

            fetch(`/admin/akun/update-ajax/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        alert('Data berhasil diperbarui!');
                        location.reload();
                    } else {
                        alert('Gagal: ' + (res.message || 'Terjadi kesalahan sistem'));
                    }
                });
        });

        // Detail View
        function viewUser(id) {
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            const body = document.getElementById('userModalBody');
            body.innerHTML = 'Memuat...';
            modal.show();
            fetch(`/admin/akun/user-detail/${id}`).then(r => r.json()).then(res => {
                if (res.success) {
                    const u = res.data;
                    body.innerHTML = `
                        <div class="mb-2"><strong>Nama:</strong> ${u.nama_display || u.nama_karyawan}</div>
                        <div class="mb-2"><strong>Username:</strong> ${u.username}</div>
                        <div class="mb-2"><strong>Role:</strong> ${u.role.toUpperCase()}</div>
                        <div class="mb-2"><strong>Status:</strong> ${u.status || 'aktif'}</div>
                    `;
                }
            });
        }

        // Delete Function
        function deleteUser(btn) {
            const name = btn.getAttribute('data-name');
            if (confirm(`Hapus akun ${name}?`)) {
                fetch(`/admin/akun/delete/${btn.getAttribute('data-id')}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(r => r.json()).then(res => {
                    if (res.success) location.reload();
                });
            }
        }
