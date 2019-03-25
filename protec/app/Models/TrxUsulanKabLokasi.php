<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxUsulanKabLokasi extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_usulan_kab_lokasi';
    protected $primaryKey = 'id_usulan_kab_lokasi';
    protected $fillable = ['id_usulan_kab', 'id_usulan_kab_lokasi', 'no_urut', 'id_lokasi', 'volume', 'id_satuan', 'uraian_lokasi'];


}