<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraSasaran extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_renstra_sasaran';
    protected $primaryKey = 'id_sasaran_renstra';
    protected $fillable = ['thn_id','no_urut','id_tujuan_renstra','id_perubahan','uraian_sasaran_renstra','created_at','updated_at','id_sasaran_rpjmd','sumber_data'];

    public function TrxRenstraTujuan()
    {
      return $this->belongsTo('App\Models\Can\TrxRenstraTujuan','id_tujuan_renstra');
    }
    public function TrxRentraKebijakans()
    {
      return $this->hasMany('App\Models\Can\TrxRentraKebijakan','id_sasaran_renstra');
    }
    public function TrxRenstraStrategis()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraStrategi','id_sasaran_renstra');
    }
    public function TrxRenstraPrograms()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraProgram','id_sasaran_renstra');
    }
    public function TrxRenstraSasaranIndikators()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraSasaranIndikator','id_sasaran_renstra');
    }
    public function TrxRpjmdSasaran()
    {
      return $this->belongsTo('App\Models\TrxRpjmdSasaran','id_sasaran_rpjmd');
    }
    public function KinTrxCascadingProgramOpds()
    {
      return $this->hasMany('App\Models\Kin\KinTrxCascadingProgramOpd','id_renstra_sasaran','id_sasaran_renstra');
    }
}
