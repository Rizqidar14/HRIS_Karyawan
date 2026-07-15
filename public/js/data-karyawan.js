// Filter & Search Logic
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('.employee-row');
            const filterBtns = document.querySelectorAll('.filter-btn');

            function filterTable() {
                const q = searchInput.value.toLowerCase();
                const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');

                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    const status = row.getAttribute('data-status');
                    const matchSearch = text.includes(q);
                    const matchFilter = (activeFilter === 'all' || status === activeFilter);
                    row.style.display = (matchSearch && matchFilter) ? '' : 'none';
                });

                // Update counter
                const visibleRows = document.querySelectorAll('.employee-row[style=""]').length;
                const counterBadge = document.querySelector('.table-header .badge');
                if (counterBadge) {
                    counterBadge.textContent = `Menampilkan ${visibleRows} Data`;
                }
            }

            searchInput.addEventListener('input', filterTable);
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterTable();
                });
            });
        });

        // Confirm Delete Function
        function confirmDelete(id, name) {
            document.getElementById('deleteEmployeeName').textContent = name;
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('admin/karyawan') }}/" + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Reset form when modal is closed
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('tambahKaryawanModal');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    const form = document.getElementById('formTambahKaryawan');
                    if (form) {
                        form.reset();
                        // Remove is-invalid class from all inputs
                        document.querySelectorAll('.is-invalid').forEach(el => {
                            el.classList.remove('is-invalid');
                        });
                    }
                });
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 5000);
            });
        }, 1000);
