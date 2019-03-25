<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class TrxRpjmdRanwalDokumen extends Model
{
    //
    public $timestamps = false;
    protected $table = 'trx_rpjmd_ranwal_dokumen';
    protected $primaryKey = 'id_rpjmd';
    protected $fillable = ['periode_awal', 'periode_akhir', 'no_perda', 'keterangan_dokumen', 'tgl_perda', 'id_revisi', 'id_status_dokumen'];

}
