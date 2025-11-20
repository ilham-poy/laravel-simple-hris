<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Account extends Model
{
    //
    use HasRoles;
    protected $fillable = [
        'name',
        'status',
        'jabatan',
        'keterangan'
    ];
}
