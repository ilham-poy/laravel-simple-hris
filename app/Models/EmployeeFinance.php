<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use App\Models\User;

class EmployeeFinance extends Model
{
    //
    use HasRoles;
    protected $fillable = [
        'user_id',
        'gaji_pokok',
        'jam_lembur',
        'gaji_lembur',
        'tidak_masuk',
        'total_gaji',
        'work_month',
        'salary_month',
        'status_pegawai'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->work_month) {
                $model->work_month = \Carbon\Carbon::parse($model->work_month)->startOfMonth();
            }
            if ($model->salary_month) {
                $model->salary_month = \Carbon\Carbon::parse($model->salary_month)->startOfMonth();
            }
        });
    }
    // public function overtime()
    // {
    //     return $this->hasMany(OvertimeEmployee::class, 'user_id', 'user_id');
    // }

    // public function getJamLemburAttribute()
    // {
    //     return $this->overtime()->sum('total_lembur');
    // }
}
