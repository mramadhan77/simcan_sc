<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinOpdSasaran extends Model
{
    protected $table = 'kin_trx_perkin_opd_sasaran';
    protected $primaryKey = 'id_perkin_sasaran';
    protected $fillable = ['id_dokumen_perkin', 'id_sasaran_renstra', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function KinTrxPerkinOpdDok()
    {
      return $this->belongsTo('App\Models\Kin\KinTrxPerkinOpdDok','id_dokumen_perkin','id_dokumen_perkin');
    }

    public function KinTrxPerkinOpdSasaranIndikators()
    {
        return $this->hasMany('App\Models\Kin\KinTrxPerkinOpdSasaranIndikator','id_perkin_sasaran','id_perkin_sasaran');
    }

}
