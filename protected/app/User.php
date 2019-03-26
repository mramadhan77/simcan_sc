<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'name', 'group_id','email', 'password','status_user',
    ];

    protected $hidden = [
        'email','password', 'remember_token',
    ];

    public function getUserSubUnit() {
        return $this->hasMany('App\Models\UserSubUnit', 'user_id', 'id');
    }

    public function getUserDesa() {
        return $this->hasMany('App\Models\UserDesa', 'user_id', 'id');
    } 

    public function getUserLevelSakip() {
        return $this->hasMany('App\Models\UserLevelSakip', 'user_id', 'id');
    }

    public function getGroupUser(){
        return $this->hasOne('App\Models\TrxGroupMenu', 'group_id', 'group_id');
    }
}
