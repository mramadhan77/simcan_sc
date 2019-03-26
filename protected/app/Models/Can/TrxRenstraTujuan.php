<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraTujuan extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_renstra_tujuan';
    protected $primaryKey = 'id_tujuan_renstra';
    protected $fillable = ['thn_id','no_urut','id_misi_renstra','id_perubahan','uraian_tujuan_renstra','sumber_data','created_at','updated_at'];

    public function TrxRenstraMisi()
    {
      return $this->belongsTo('App\Models\Can\TrxRenstraMisi','id_misi_renstra');
    }

    public function TrxRenstraSasarans()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraSasaran','id_tujuan_renstra');
    }

    public function TrxRenstraTujuanIndikators()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraTujuanIndikator','id_tujuan_renstra');
    }

}
