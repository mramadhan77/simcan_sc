<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxCascadingKegiatanOpd extends Model
{
    protected $table = 'kin_trx_cascading_kegiatan_opd';
    protected $primaryKey = 'id_hasil_kegiatan';
    protected $fillable = ['id_unit', 'id_hasil_program', 'id_renstra_kegiatan', 'uraian_hasil_kegiatan'];

    public $timestamps = false;

    public function KinTrxCascadingProgramOpd()
 	{
      return $this->belongsTo('App\Models\Kin\KinTrxCascadingProgramOpd','uraian_hasil_program');
    }

    public function KinTrxCascadingIndikatorKegiatanOpds()
  	{
      return $this->hasMany('App\Models\Kin\KinTrxCascadingIndikatorKegiatanOpd','id_hasil_kegiatan','id_hasil_kegiatan');
    }
}
