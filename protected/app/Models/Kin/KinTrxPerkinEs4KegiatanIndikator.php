<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinEs4KegiatanIndikator extends Model
{
    protected $table = 'kin_trx_perkin_es4_kegiatan_indikator';
    protected $primaryKey = 'id_perkin_indikator';
    protected $fillable = ['id_perkin_kegiatan', 'id_indikator_kegiatan_renstra', 'target_tahun', 'target_t1', 
    'target_t2', 'target_t3', 'target_t4', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function KinTrxPerkinEs3Program()
    {
      return $this->belongsTo('App\Models\Kin\KinTrxPerkinEs3Program','id_perkin_program','id_perkin_program');
    }

}
