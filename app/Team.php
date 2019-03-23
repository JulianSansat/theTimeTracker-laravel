<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Team extends Model
{
    use SoftDeletes;

    const ORIGINS = [
        '0' => 'storage',
        '1' => 'remote'
    ];

    const VALIDATION = [
        'name'        => 'required',
        'description' => 'required',
        'logo_origin' => 'required',
        'logo_path'   => 'required',
        'author_id'   => 'required'
    ];

    protected $guarded = [];

    protected $dates = [
        'deleted_at'
    ];

    public function getOriginNameAttribute()
    {
        if (array_key_exists($this->origin, self::ORIGINS)) {
            return self::ORIGINS[$this->origin];
        }

        return 'Origin not defined';
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTrashed();
    }
}