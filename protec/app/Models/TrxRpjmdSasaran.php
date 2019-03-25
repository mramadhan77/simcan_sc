<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRpjmdSasaran extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_rpjmd_sasaran';
    protected $primaryKey = 'id_sasaran_rpjmd';
    protected $fillable = ['thn_id_rpjmd','no_urut','id_tujuan_rpjmd','id_sasaran_rpjmd','id_perubahan','uraian_sasaran_rpjmd','sumber_data','created_at','updated_at'];

    public function trx_rpjmd_tujuan()
    {
      return $this->belongsTo('App\Models\TrxRpjmdTujuan','id_tujuan_rpjmd');
    }
    public function trx_rpjmd_kebijakan()
    {
      return $this->hasMany('App\Models\TrxRpjmdKebijakan','id_sasaran_rpjmd');
    }
    public function trx_rpjmd_strategi()
    {
      return $this->hasMany('App\Models\TrxRpjmdStrategi','id_sasaran_rpjmd');
    }
    public function TrxRpjmdPrograms()
    {
      return $this->hasMany('App\Models\TrxRpjmdProgram','id_sasaran_rpjmd');
    }
    public function TrxRenstraSasarans()
    {
      return $this->hasMany('App\Models\Can\TrxRenstraSasaran','id_sasaran_rpjmd');
    }
}
