<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRpjmdRanwalVisi extends Model
{
    
    public $timestamps = false;
    protected $table = 'trx_rpjmd_ranwal_visi';
    protected $primaryKey = 'id_visi_rpjmd';
    protected $fillable = ['thn_id', 'no_urut', 'id_rpjmd', 'id_perubahan', 'uraian_visi_rpjmd'];
}
