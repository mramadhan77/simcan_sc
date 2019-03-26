<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefUserRole extends Model
{
    protected $table = 'ref_user_role';
    protected $primaryKey = 'id';
    public $fillable = ['id', 'uraian_peran', 'tambah', 'edit', 'hapus', 'lihat', 'reviu', 'posting', 'status_role'];

    public $timestamps = true;

    public function getGroup() {
        return $this->hasMany('App\Models\RefGroup', 'id_roles', 'id');
    }
}
