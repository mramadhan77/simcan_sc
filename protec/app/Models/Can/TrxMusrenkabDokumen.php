<?php

namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxMusrenkabDokumen extends Model
{
    protected $table = 'trx_musrenkab_dokumen';
    protected $fillable = ['id_dokumen_rkpd', 'nomor_rkpd', 'tanggal_rkpd', 'tahun_rkpd', 'uraian_perkada', 'id_unit_perencana', 'jabatan_tandatangan', 'nama_tandatangan', 'nip_tandatangan', 'flag'];
    protected $primaryKey = 'id_dokumen_rkpd';
    public $timestamps = false;


}
