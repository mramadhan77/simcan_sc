<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxCascadingProgramOpd extends Model
{
    protected $table = 'kin_trx_cascading_program_opd';
    protected $primaryKey = 'id_hasil_program';
    protected $fillable = ['id_unit', 'id_renstra_sasaran', 'id_renstra_program', 'uraian_hasil_program'];

    public $timestamps = false;

    public function TrxRenstraSasaran()
  	{
      return $this->belongsTo('App\Models\Can\TrxRenstraSasaran','id_sasaran_renstra','id_renstra_sasaran');
    }

  	public function KinTrxCascadingIndikatorProgramOpds()
  	{
      return $this->hasMany('App\Models\Kin\KinTrxCascadingIndikatorProgramOpd','id_hasil_program','id_hasil_program');
    }

  	public function KinTrxCascadingKegiatanOpds()
  	{
      return $this->hasMany('App\Models\Kin\KinTrxCascadingKegiatanOpd','id_hasil_program','id_hasil_program');
    }
}
