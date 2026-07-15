<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    // Nama tabel di database (sesuaikan jika nama tabel Anda berbeda)
    protected $table = 'cuti_karyawan';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'id_karyawan',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'status', // 'Pending', 'Disetujui', 'Ditolak'
        'catatan_admin'
    ];

    /**
     * Relasi ke model Karyawan
     * Memungkinkan kita mengambil data karyawan lewat $cuti->karyawan->nama_karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    /**
     * Scope untuk memudahkan filter status di Controller atau View
     * Contoh penggunaan: Cuti::pending()->count();
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }
}
