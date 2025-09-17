<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class OvertimeEmployee extends Model
{
    //
    use HasFactory, HasRoles;
    protected $fillable = [
        'user_id',
        'tanggal',
        'total_lembur',
        'status' => 'pending'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
