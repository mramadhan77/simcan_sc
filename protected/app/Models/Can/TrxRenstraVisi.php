<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraVisi extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_renstra_visi';
    protected $primaryKey = 'id_visi_renstra';
    protected $fillable = ['thn_id','no_urut','id_visi_renstra','id_unit','id_perubahan','thn_awal_renstra','thn_akhir_renstra','uraian_visi_renstra','id_status_dokumen','sumber_data','created_at','updated_at'];

	public function TrxRenstraMisis()
  {
      return $this->hasMany('App\Models\Can\TrxRenstraMisi','id_visi_renstra');
    }

  public function RefUnit()
    {
      return $this->belongsTo('App\Models\RefUnit','id_unit');
    }

}
