<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxCascadingIndikatorProgramOpd extends Model
{
    protected $table = 'kin_trx_cascading_indikator_program_pd';
    protected $primaryKey = 'id_indikator_program_pd';
    protected $fillable = ['id_hasil_program', 'id_renstra_program_indikator'];

    public $timestamps = false;

    public function KinTrxCascadingProgramOpd()
  	{
      return $this->belongsTo('App\Models\Kin\KinTrxCascadingProgramOpd','id_hasil_program');
    }
}
