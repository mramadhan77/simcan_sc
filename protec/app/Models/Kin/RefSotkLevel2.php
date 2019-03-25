<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class RefSotkLevel2 extends Model
{
    protected $table = 'ref_sotk_level_2';
    protected $primaryKey = 'id_sotk_es3';
    protected $fillable = ['id_sotk_es3', 'id_sotk_es2', 'nama_eselon', 'tingkat_eselon', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;
    
    // public function level2s()
    // {
    //   return $this->belongsTo('App\Models\Kin\RefSotkLevel1','id_sotk_es2');
    // }

    public function level3s()
    {
      return $this->hasMany('App\Models\Kin\RefSotkLevel3','id_sotk_es3','id_sotk_es3');
    }

}
