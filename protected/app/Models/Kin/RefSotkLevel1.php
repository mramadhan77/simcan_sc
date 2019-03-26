<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class RefSotkLevel1 extends Model
{
    protected $table = 'ref_sotk_level_1';
    protected $primaryKey = 'id_sotk_es2';
    protected $fillable = ['id_sotk_es2', 'id_unit', 'nama_eselon', 'tingkat_eselon', 'status_data','created_at', 'updated_at'];

    public $timestamps = true;
    
    // public function ref_unit()
    // {
    //   return $this->belongsTo('App\Models\RefUnit','id_unit');
    // }

    public function level2s()
    {
      return $this->hasMany('App\Models\Kin\RefSotkLevel2','id_sotk_es2','id_sotk_es2');
    }

}
