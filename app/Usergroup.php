<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Usergroup extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    const VALIDATION = [
        "name"     => 'required|min:3|max:32',
        "accesses" => 'present|nullable|json',
    ];

    public function user()
    {
        return $this->hasMany(User::class)->withTrashed();
    }
}
