<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class RefSotkLevel3 extends Model
{
    protected $table = 'ref_sotk_level_3';
    protected $primaryKey = 'id_sotk_es4';
    protected $fillable = ['id_sotk_es3', 'id_sotk_es4', 'nama_eselon', 'tingkat_eselon', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;
    
    public function level2s()
    {
      return $this->belongsTo('App\Models\Kin\RefSotkLevel2','id_sotk_es3','id_sotk_es3');
    }

}
