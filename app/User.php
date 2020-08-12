<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function passengers()
    {
        return $this->belongsToMany(User::class, 'user_connections', 'rider_id', 'passenger_id')->withPivot("status");
    }

    public function riders()
    {
        return $this->belongsToMany(User::class, 'user_connections', 'passenger_id', 'rider_id')->withPivot("status");;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, "user_role");
    }
    public function commuteInfos()
    {
        return $this->hasMany('App\CommuteInfo');
    }

    public function addNew($input)
    {
        $check = static::where('facebook_id',$input['facebook_id'])->first();


        if(is_null($check)){
            return static::create($input);
        }


        return $check;
    }
}
