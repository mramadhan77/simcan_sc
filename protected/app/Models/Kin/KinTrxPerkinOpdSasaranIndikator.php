<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinOpdSasaranIndikator extends Model
{
    protected $table = 'kin_trx_perkin_opd_sasaran_indikator';
    protected $primaryKey = 'id_perkin_indikator';
    protected $fillable = ['id_perkin_sasaran', 'id_indikator_sasaran_renstra', 'target_tahun', 'target_t1', 
    'target_t2', 'target_t3', 'target_t4', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function KinTrxPerkinOpdSasaran()
    {
      return $this->belongsTo('App\Models\Kin\KinTrxPerkinOpdSasaran','id_perkin_sasaran','id_perkin_sasaran');
    }

}
