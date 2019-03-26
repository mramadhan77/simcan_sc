<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxPerkinOpdDok extends Model
{
    protected $table = 'kin_trx_perkin_opd_dok';
    protected $primaryKey = 'id_dokumen_perkin';
    protected $fillable = ['id_sotk_es2', 'tahun', 'no_dokumen', 'tgl_dokumen', 'tanggal_mulai', 'id_pegawai', 
    'nama_penandatangan', 'jabatan_penandatangan', 'pangkat_penandatangan', 'uraian_pangkat_penandatangan', 
    'nip_penandatangan', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function RefSotkLevel1()
    {
      return $this->belongsTo('App\Models\Kin\RefSotkLevel1','id_sotk_es2','id_sotk_es2');
    }

    public function KinTrxPerkinOpdSasarans()
    {
        return $this->hasMany('App\Models\Kin\KinTrxPerkinOpdSasaran', 'id_dokumen_perkin', 'id_dokumen_perkin');
    }
}
