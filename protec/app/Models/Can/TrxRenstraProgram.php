<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraProgram extends Model
{
    public $timestamps = true;
    protected $table = 'trx_renstra_program';
    protected $primaryKey = 'id_program_renstra';
    protected $fillable = ['thn_id','no_urut','id_sasaran_renstra','id_program_rpjmd','id_program_ref','id_perubahan','uraian_program_renstra','pagu_tahun1',
        'pagu_tahun2','pagu_tahun3','pagu_tahun4','pagu_tahun5','sumber_data','created_at','updated_at','uraian_sasaran_program'];

    public function sasaran()
    {
      return $this->belongsTo('App\Models\Can\TrxRenstraSasaran','id_sasaran_renstra');
    }

    public function TrxRpjmdProgram()
    {
      return $this->belongsTo('App\Models\TrxRpjmdProgram','id_program_rpjmd');
    }

    public function RefProgram()
    {
      return $this->belongsTo('App\Models\RefProgram','id_program_ref','id_program');
    }

    public function TrxRenstraProgramIndikators()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraProgramIndikator','id_program_renstra');
    }

    public function TrxRenstraKegiatans()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraKegiatan','id_program_renstra');
    }

}
