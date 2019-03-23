<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'password', 'remember_token',
    ];

    protected $dates = [
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const VALIDATION = [
        "name"     => 'required|min:3|max:32',
        "accesses" => 'present|nullable|json',
    ];

    public function getUserAccessAttribute()
    {
        return $this->user['accesses'];
    }

    public function getAllAccessAttribute()
    {
        $profile_accesses = json_decode($this->usergroup->accesses, true) ?? [];
        $user_accesses    = json_decode($this->accesses, true) ?? [];
        
        $all_accesses     = $profile_accesses;
        foreach ($user_accesses as $key => $access) {
            if (!array_key_exists($key, $profile_accesses)) {
                $all_accesses[$key] = $access;
                continue;
            }
            $all_accesses[$key] = array_merge($all_accesses[$key], $access);
        }
        return $all_accesses;
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
}
