<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageEmployee extends Model
{
    use HasFactory;

    // Nama tabel eksplisit (opsional, tapi aman)
    protected $table = 'manage_employees';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'email_kantor',
        'email_pribadi',
        'no_hp',
        'no_keluarga_1',
        'no_keluarga_2',
        'jenis_kelamin',
        'alamat',
        // Kolom SIM/SIO (Hasil gabungan dari skema sebelumnya)
        'nomor_sim',
        'tipe_sim',
        'expired_sim',
    ];

    /**
     * Casting tanggal kadaluarsa SIM ke format Carbon/Date
     */
    protected $casts = [
        'expired_sim' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
