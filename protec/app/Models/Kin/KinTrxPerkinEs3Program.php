<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinEs3Program extends Model
{
    protected $table = 'kin_trx_perkin_es3_program';
    protected $primaryKey = 'id_perkin_program';
    protected $fillable = ['id_dokumen_perkin', 'id_perkin_program_opd', 'id_program_renstra', 'pagu_tahun', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function KinTrxPerkinEs3Dok()
    {
      return $this->belongsTo('App\Models\Kin\KinTrxPerkinEs3Dok','id_dokumen_perkin','id_dokumen_perkin');
    }

    public function KinTrxPerkinEs3ProgramIndikators()
    {
        return $this->hasMany('App\Models\Kin\KinTrxPerkinEs3ProgramIndikator','id_perkin_program','id_perkin_program');
    }

}
