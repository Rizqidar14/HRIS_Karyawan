document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('statusSelect');
    const lemburArea = document.getElementById('lemburArea');
    const keteranganArea = document.getElementById('keteranganArea');

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'hadir') {
                lemburArea.style.display = 'block';
                keteranganArea.style.display = 'none';
            } else if (this.value === 'alpha') {
                lemburArea.style.display = 'none';
                keteranganArea.style.display = 'none';
            } else {
                lemburArea.style.display = 'none';
                keteranganArea.style.display = 'block';
            }
        });
    }
});
