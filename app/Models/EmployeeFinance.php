<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gaji_pokok',
        'jam_lembur',
        'gaji_lembur',
        'tidak_masuk',
        'total_gaji',
        'work_month',
        'salary_month',
        'status_pegawai',
    ];

    /**
     * Casting tipe data otomatis
     */
    protected $casts = [
        'gaji_pokok'   => 'integer',
        'jam_lembur'   => 'integer',
        'gaji_lembur'  => 'integer',
        'tidak_masuk'  => 'integer',
        'total_gaji'   => 'integer',
        'work_month'   => 'date',
        'salary_month' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booted event untuk standarisasi tanggal awal bulan
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->work_month) {
                $model->work_month = Carbon::parse($model->work_month)->startOfMonth();
            }
            if ($model->salary_month) {
                $model->salary_month = Carbon::parse($model->salary_month)->startOfMonth();
            }
        });
    }
}
