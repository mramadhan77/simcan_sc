<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranProgramPd extends Model
{
    public $timestamps = true;
    protected $table = 'trx_anggaran_program_pd';
    protected $primaryKey = 'id_program_pd';
    protected $fillable = ['id_program_pd',
        'id_pelaksana_anggaran',
        'kd_dokumen_keu',
        'jns_dokumen_keu',
        'id_dokumen_keu',
        'id_perubahan',
        'tahun_anggaran',
        'jenis_belanja',
        'no_urut',
        'id_unit',
        'id_program_pd_rkpd_final',
        'id_program_renstra',
        'uraian_program_renstra',
        'id_program_ref',
        'pagu_rkpd_final',
        'pagu_anggaran',
        'sumber_data',
        'status_pelaksanaan',
        'ket_usulan',
        'status_data',
        'created_at',
        'updated_at',
        'checksum',];


}