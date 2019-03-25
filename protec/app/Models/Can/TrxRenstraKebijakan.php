<?php
namespace App\Models\Can;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRenstraKebijakan extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_renstra_kebijakan';
    protected $primaryKey = 'id_kebijakan_renstra';
    protected $fillable = ['thn_id','no_urut','id_sasaran_renstra','id_perubahan','uraian_kebijakan_renstra','sumber_data','created_at','updated_at'];

    public function trx_renstra_sasaran()
    {
      return $this->belongsTo('App\Models\TrxRenstraSasaran','id_sasaran_renstra');
    }


}
