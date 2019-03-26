<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinEs4Dok extends Model
{
    protected $table = 'kin_trx_perkin_es4_dok';
    protected $primaryKey = 'id_dokumen_perkin';
    protected $fillable = ['id_sotk_es4', 'tahun', 'no_dokumen', 'tgl_dokumen', 'tanggal_mulai', 'id_pegawai', 
    'nama_penandatangan', 'jabatan_penandatangan', 'pangkat_penandatangan', 'uraian_pangkat_penandatangan', 
    'nip_penandatangan', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function RefSotkLevel2()
    {
      return $this->belongsTo('App\Models\Kin\RefSotkLevel2','id_sotk_es3','id_sotk_es3');
    }

    public function KinTrxPerkinEs3Programs()
    {
        return $this->hasMany('App\Models\Kin\KinTrxPerkinEs3Program', 'id_dokumen_perkin', 'id_dokumen_perkin');
    }
}
