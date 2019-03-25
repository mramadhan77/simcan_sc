<?php

namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranDokumen extends Model
{
    protected $table = 'trx_anggaran_dokumen';
    protected $fillable = ['id_dokumen_keu', 'jns_dokumen_keu', 'kd_dokumen_keu', 'id_perubahan', 'tahun_anggaran', 'nomor_keu', 'id_dokumen_ref',
        'tanggal_keu', 'uraian_perkada', 'id_unit_ppkd', 'jabatan_tandatangan', 'nama_tandatangan', 'nip_tandatangan', 'flag', 'created_at', 'updated_at'];
    protected $primaryKey = 'id_dokumen_keu';
    public $timestamps = true;


}
