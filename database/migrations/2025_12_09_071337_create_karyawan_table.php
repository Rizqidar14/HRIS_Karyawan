<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel lama jika ada
        Schema::dropIfExists('karyawan');

        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('id_karyawan', 50)->unique();
            $table->string('foto', 255)->nullable();
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('divisi', 50);
            $table->string('jabatan', 50)->nullable();
            $table->date('tanggal_bergabung');
            $table->enum('status', ['Aktif', 'Non-Aktif', 'Cuti'])->default('Aktif');
            $table->timestamps();

            $table->index('divisi');
            $table->index('status');
            $table->index('tanggal_bergabung');
        });

        // Reset auto increment
        DB::statement('ALTER TABLE karyawan AUTO_INCREMENT = 1');
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
