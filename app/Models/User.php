<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'karyawan_id',
        'username',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_encrypted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke tabel karyawan (belongsTo karena users punya karyawan_id)
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    /**
     * Ambil nama karyawan dari relasi
     * Jika tidak ada relasi, ambil dari field name
     */
    public function getNamaKaryawanAttribute()
    {
        return $this->karyawan ? $this->karyawan->nama_karyawan : $this->name;
    }

    /**
     * Ambil status karyawan dari relasi
     */
    public function getStatusKaryawanAttribute()
    {
        return $this->karyawan ? ($this->karyawan->status ?? 'Aktif') : ($this->status ?? 'Aktif');
    }
}
