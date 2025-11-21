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
        'status_pegawai'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
