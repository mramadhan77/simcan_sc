<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs2Dok extends Model
{
    protected $table = 'kin_trx_real_es2_dok';
    protected $primaryKey = 'id_dokumen_real';
    protected $fillable = ['id_dokumen_perkin', 'id_sotk_es2', 'tahun', 'triwulan', 'no_dokumen', 'tgl_dokumen', 
    'id_pegawai', 'nama_penandatangan', 'jabatan_penandatangan', 'pangkat_penandatangan', 
    'uraian_pangkat_penandatangan', 'nip_penandatangan', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

}
