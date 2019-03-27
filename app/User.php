<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Team;
use App\Log;
use App\Shift;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name','email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'password_confirmation', 'remember_token',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $with = [
        'teams'
    ];

    const VALIDATION = [
        'first_name'     => 'required|min:3|max:32',
        'last_name'      => 'required|min:3|max:32',
        'email'         => 'required|string|email|max:64|unique:users',
        'password'      => 'required|string|min:6|max:64|confirmed'
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function getAllAccessAttribute()
    {
        $profile_accesses = json_decode($this->usergroup->accesses, true) ?? [];

        return $profile_accesses;
    }

    public function checkAccess($model, $action)
    {
        $accesses = $this->all_access;
        if (!($accesses)) {
            return false;
        }
        
        if (isset($accesses[$model][$action])) {
            return $accesses[$model][$action];
        }
        
        return false;
    }

    public function usergroup()
    {
        return $this->belongsTo(Usergroup::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function shift()
    {
        return $this->hasOne(Shift::class);
    }
}
