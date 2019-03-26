<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinEs3Kegiatan extends Model
{
    protected $table = 'kin_trx_perkin_es3_kegiatan';
    protected $primaryKey = 'id_perkin_kegiatan';
    protected $fillable = ['id_perkin_program', 'id_kegiatan_renstra', 'id_sotk_es4', 'status_data','pagu_tahun', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function KinTrxPerkinEs3Program()
    {
      return $this->belongsTo('App\Models\Kin\KinTrxPerkinEs3Program','id_perkin_program','id_perkin_program');
    }

}
