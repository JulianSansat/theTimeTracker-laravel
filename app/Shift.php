<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Shift extends Model
{
    
    protected $guarded = [];

    const VALIDATION = [
        'start'     => 'required|date_format:Y-m-d H:i:s',
        'user_id'   => 'required|unique|integer|min:1|digits_between:1,20',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    
}