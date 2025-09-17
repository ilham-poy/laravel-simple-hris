<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;

class ManageEmployee extends Model
{
    //
    use HasFactory, HasRoles;
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'email_kantor',
        'email_pribadi',
        'no_hp',
        'no_keluarga_1',
        'no_keluarga_2',
        'jenis_kelamin',
        'alamat'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
