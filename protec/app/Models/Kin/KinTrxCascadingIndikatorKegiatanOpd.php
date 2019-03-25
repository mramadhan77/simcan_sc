<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxCascadingIndikatorKegiatanOpd extends Model
{
    protected $table = 'kin_trx_cascading_indikator_kegiatan_pd';
    protected $primaryKey = 'id_indikator_kegiatan_pd';
    protected $fillable = ['id_hasil_kegiatan', 'id_renstra_kegiatan_indikator'];

    public $timestamps = false;

    public function KinTrxCascadingKegiatanOpd()
  	{
      return $this->belongsTo('App\Models\Kin\KinTrxCascadingKegiatanOpd','id_hasil_kegiatan');
    }
}
