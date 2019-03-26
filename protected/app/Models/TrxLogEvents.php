<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxLogEvents extends Model
{
    protected $table = 'trx_log_events';
    protected $primaryKey = 'id';
    protected $fillable = ['code_events','discription','created_at','operate'];

    public $timestamps = true;

}
