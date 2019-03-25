<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefGroup extends Model
{
    protected $table = 'ref_group';
    protected $primaryKey = 'id';
    public $fillable = ['id', 'name', 'keterangan', 'id_roles'];

    public $timestamps = false;

    public function getUser() {
        return $this->hasMany('App\User', 'group_id', 'id');
    }

    public function getRoleGroup() {
        return $this->hasOne('App\Models\RefUserRole', 'id_', 'id_roles');
    }


}
