<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinOpdProgram extends Model
{
    protected $table = 'kin_trx_perkin_opd_program';
    protected $primaryKey = 'id_perkin_program';
    protected $fillable = ['id_perkin_sasaran', 'id_program_renstra', 'id_sotk_es3', 'status_data','pagu_tahun', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function KinTrxPerkinOpdSasaran()
    {
      return $this->belongsTo('App\Models\Kin\KinTrxPerkinOpdSasaran','id_perkin_sasaran','id_perkin_sasaran');
    }

}
