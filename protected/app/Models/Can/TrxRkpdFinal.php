<?php

namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdFinal extends Model
{
    protected $table = 'trx_rkpd_final';
    protected $primaryKey = 'id_rkpd_rancangan';
    protected $fillable = ['id_rkpd_rancangan', 'id_rkpd_ranwal','id_forum_rkpdprog','no_urut','tahun_rkpd','id_rkpd_rpjmd',
    	'thn_id_rpjmd','id_visi_rpjmd','id_misi_rpjmd','id_tujuan_rpjmd','id_sasaran_rpjmd','jenis_belanja',
    	'id_program_rpjmd','uraian_program_rpjmd','pagu_rpjmd','pagu_ranwal','keterangan_program','status_data',
    	'status_pelaksanaan','ket_usulan', 'sumber_data','id_dokumen'];
    public $timestamps = false;


}
