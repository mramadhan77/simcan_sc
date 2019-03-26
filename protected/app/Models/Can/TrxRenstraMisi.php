<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraMisi extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_renstra_misi';
    protected $primaryKey = 'id_misi_renstra';
    protected $fillable = ['thn_id','no_urut','id_visi_renstra','id_perubahan','uraian_misi_renstra','sumber_data','created_at','updated_at'];

	public function TrxRenstraVisi()
  {
      return $this->belongsTo('App\Models\Can\TrxRenstraVisi','id_visi_renstra');
    }

  public function TrxRenstraTujuans()
  {
      return $this->hasMany('App\Models\Can\TrxRenstraTujuan','id_misi_renstra','id_misi_renstra');
    }
}
