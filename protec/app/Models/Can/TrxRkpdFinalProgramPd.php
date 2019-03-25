<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRkpdFinalProgramPd extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_rkpd_final_program_pd';
    protected $primaryKey = 'id_program_pd';
    protected $fillable = ['id_program_pd', 'id_pelaksana_rkpd', 'tahun_forum', 'jenis_belanja', 'no_urut', 'id_unit', 'id_rkpd_rancangan', 
    'id_renja_program', 'id_program_renstra', 'uraian_program_renstra', 
	'id_program_ref', 'pagu_tahun_renstra', 'pagu_forum', 'sumber_data', 'status_pelaksanaan', 
	'ket_usulan', 'status_data', 'id_dokumen'];


}