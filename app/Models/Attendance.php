<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OvertimeEmployee;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_schedule_id', // Relasi ke jadwal shift
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'durasi_keterlambatan_menit',
        'foto_masuk',
        'foto_keluar',
        'lampiran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'durasi_keterlambatan_menit' => 'integer',
    ];

    /**
     * Relasi ke User (Karyawan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jadwal Shift Kerja
     */
    public function schedule()
    {
        return $this->belongsTo(EmployeeSchedule::class, 'employee_schedule_id');
    }
}
