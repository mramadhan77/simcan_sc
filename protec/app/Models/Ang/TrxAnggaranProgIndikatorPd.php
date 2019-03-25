<?php
namespace App\Models\Ang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxAnggaranProgIndikatorPd extends Model
{
    public $timestamps = false;
    protected $table = 'trx_anggaran_prog_indikator_pd';
    protected $primaryKey = 'id_indikator_program';
    protected $fillable = ['id_indikator_program',
		'id_program_pd',
		'id_indikator_rkpd_final',
		'tahun_anggaran',
		'no_urut',
		'id_program_renstra',
		'id_perubahan',
		'kd_indikator',
		'uraian_indikator_program',
		'tolok_ukur_indikator',
		'target_renstra',
		'target_renja',
		'indikator_output',
		'id_satuan_ouput',
		'indikator_input',
		'target_input',
		'id_satuan_input',
		'status_data',
		'sumber_data',];


}