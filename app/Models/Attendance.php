<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Attendance extends Model
{
    //
    use HasRoles;
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
