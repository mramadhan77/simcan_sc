<?php

namespace App\Models\Kin;

use Illuminate\Database\Eloquent\Model;

class KinTrxRealEs3Dok extends Model
{
    protected $table = 'kin_trx_real_es3_dok';
    protected $primaryKey = 'id_dokumen_real';
    protected $fillable = ['id_dokumen_perkin', 'id_sotk_es3', 'tahun', 'triwulan', 'no_dokumen', 'tgl_dokumen', 
    'id_pegawai', 'nama_penandatangan', 'jabatan_penandatangan', 'pangkat_penandatangan', 
    'uraian_pangkat_penandatangan', 'nip_penandatangan', 'status_data', 'created_at', 'updated_at'];

    public $timestamps = true;

}
