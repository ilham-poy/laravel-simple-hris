<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    /**
     * Nilai default atribut saat instansiasi model baru
     */
    protected $attributes = [
        'status' => 'pending',
        'shift_type' => 'pagi',
        'total_lembur' => 0.00,
    ];

    /**
     * Daftar kolom mass-assignment (Penulisan array datar)
     */
    protected $fillable = [
        'user_id',
        'tanggal',
        'shift_type',
        'total_lembur',
        'keterangan_lembur',
        'status',
    ];

    /**
     * Casting tipe data otomatis
     */
    protected $casts = [
        'tanggal'      => 'date',
        'total_lembur' => 'decimal:2',
    ];

    /**
     * Relasi ke User (Karyawan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Absensi
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_schedule_id');
    }
}
