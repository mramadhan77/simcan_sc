<?php

namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranProgram extends Model
{
    protected $table = 'trx_anggaran_program';
    protected $primaryKey = 'id_anggaran_pemda';
    protected $fillable = ['id_anggaran_pemda',
            'id_dokumen_keu',
            'id_rkpd_ranwal',
            'id_rkpd_final',
            'no_urut',
            'jenis_belanja',
            'tahun_anggaran',
            'id_rkpd_rpjmd',
            'thn_id_rpjmd',
            'id_visi_rpjmd',
            'id_misi_rpjmd',
            'id_tujuan_rpjmd',
            'id_sasaran_rpjmd',
            'id_program_rpjmd',
            'uraian_program_rpjmd',
            'pagu_rkpd',
            'pagu_keuangan',
            'keterangan_program',
            'status_pelaksanaan',
            'status_data',
            'ket_usulan',
            'sumber_data',
            'created_at',
            'updated_at',];
    public $timestamps = true;


}
