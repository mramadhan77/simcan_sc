<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxUsulanKab extends Model
{
    //
    public $timestamps = true;
    protected $table = 'trx_usulan_kab';
    protected $primaryKey = 'id_usulan_kab';
    protected $fillable = ['id_usulan_kab', 'id_tahun', 'id_kab', 'id_unit', 'no_urut', 'judul_usulan', 'uraian_usulan', 'volume', 'id_satuan', 'pagu', 'created_at', 'updated_at', 'entry_by', 'sumber_usulan'];


}