<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRpjmdRanwalMisi extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_rpjmd_ranwal_misi';
    protected $primaryKey = 'id_misi_rpjmd';
    protected $fillable = ['thn_id_rpjmd', 'no_urut', 'id_visi_rpjmd', 'id_perubahan', 'uraian_misi_rpjmd'];

}
