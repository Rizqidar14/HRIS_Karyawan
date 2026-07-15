import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // === FILE CSS ===
                'resources/css/app.css',
                'resources/css/buat-akun.css',
                'resources/css/cuti-karyawan.css',
                'resources/css/cuti-personal.css',
                'resources/css/dashboard-user.css',
                'resources/css/dashboard.css',
                'resources/css/data-karyawan.css',
                'resources/css/detail-akun.css',
                'resources/css/detail-karyawan.css',
                'resources/css/detail-klaim.css',
                'resources/css/edit-karyawan.css',
                'resources/css/klaim-karyawan.css',
                'resources/css/klaim-personal.css',
                'resources/css/login.css',
                'resources/css/payslip.css',
                'resources/css/penggajian_karyawan.css',
                'resources/css/riwayat-klaim.css',
                'resources/css/riwayat-personal.css',
                'resources/css/riwayat.css',

                // === FILE JS ===
                'resources/js/absensi.js',
                'resources/js/app.js',
                'resources/js/buat-akun.js',
                'resources/js/data-karyawan.js',
                'resources/js/detail-akun.js',
                'resources/js/klaim-personal.js',
                'resources/js/penggajian_karyawan.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Mengatur file JavaScript murni masuk ke folder js/ tanpa angka 2
                entryFileNames: 'assets/js/[name].js',
                chunkFileNames: 'assets/js/[name].js',

                // Mengatur asset CSS masuk ke folder css/ dengan nama aslinya
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'assets/css/[name].[ext]';
                    }
                    return 'assets/[name].[ext]';
                },
            },
        },
    },
});
