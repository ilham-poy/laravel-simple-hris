<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $table = 'employee_schedules';

    protected $attributes = [
        'shift_type'   => 'pagi',
        'total_lembur' => 0.00,
        'status'       => 'approved',
    ];

    protected $fillable = [
        'user_id',
        'tanggal',
        'shift_type',
        'jam_masuk',
        'jam_keluar',
        'total_lembur',
        'keterangan_lembur',
        'status',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'total_lembur' => 'decimal:2',
    ];

    /**
     * Relasi ke Karyawan / User
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
