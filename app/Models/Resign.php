<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;

class Resign extends Model
{
    use HasRoles;
    protected $fillable = [
        'user_id',
        'status',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
