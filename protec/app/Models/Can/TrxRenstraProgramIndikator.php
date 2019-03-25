<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraProgramIndikator extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_renstra_program_indikator';
    protected $primaryKey = 'id_indikator_program_renstra';
    protected $fillable = ['thn_id','no_urut','id_program_renstra','id_perubahan','kd_indikator','uraian_indikator_program_renstra','id_indikator_sasaran_renstra',
    'tolok_ukur_indikator','angka_awal_periode','angka_tahun1','angka_tahun2','angka_tahun3','angka_tahun4','angka_tahun5','angka_akhir_periode','created_at','updated_at','sumber_data'];

    public function trx_renstra_program()
    {
      return $this->belongsTo('App\Models\Can\TrxRenstraProgram','id_program_renstra');
    }


}
