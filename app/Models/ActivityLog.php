<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    //
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'performed_by'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
