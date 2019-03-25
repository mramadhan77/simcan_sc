<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxForumSkpdProgramIndikator extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_forum_skpd_program_indikator';
    protected $primaryKey = 'id_indikator_program';
    protected $fillable = [
			'tahun_renja',
			'no_urut',
			'id_forum_program',
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
			'sumber_data'
			];


}