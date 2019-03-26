<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDesa extends Model
{
    protected $table = 'user_desa';
    protected $primaryKey = 'id_user_wil';

    public $timestamps = false;

    public $fillable = ['id_user_wil',
    	'user_id',
    	'kd_kecamatan',
        'kd_desa'
    ];

    public function getKabupaten() {
        // return $this->hasOne('App\Models\RefKecamatan', 'id_kecamatan', 'kd_kecamatan');
    }

    public function getKecamatan() {
        return $this->hasOne('App\Models\RefKecamatan', 'id_kecamatan', 'kd_kecamatan');
    }    

    public function getDesa() {
        return $this->hasOne('App\Models\RefDesa', 'id_kecamatan', 'kd_kecamatan')->where('kd_desa', $this->kd_desa);
    }
}
