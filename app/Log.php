<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\User;

class Log extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = [
        'deleted_at', 'start', 'finish'
    ];

    protected $appends = [
        'time'
    ];

    public function getTimeAttribute()
    {
        $totalDuration = $this->finish->diffInSeconds($this->start);
        return gmdate('H:i:s', $totalDuration);
    }


    const VALIDATION = [
        'start'     => 'required|date_format:Y-m-d H:i:s',
        'finish'    => 'required|date_format:Y-m-d H:i:s',
        'user_id'   => 'required|integer|min:1|digits_between:1,20',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}