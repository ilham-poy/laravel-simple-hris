<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resign extends Model
{
    use HasFactory;

    protected $table = 'resigns';

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $fillable = [
        'user_id',
        'jenis_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_hari',
        'alasan',
        'lampiran_surat',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'total_hari'      => 'integer',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
