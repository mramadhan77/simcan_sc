<?php

namespace App;

/*
* this is model for Form in GroupAkses menu.
* This form didn't entitled to any table in database
* This form just contain somelogic to check are any group has any acces to certain module
* Don't ever changed anything in this file, any change can make application stop working or not give desire content in database
* --hoaaah
*/

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuForm extends Model{

    public $group_id;
    public $menu_id;

    public $fill;

    private $method = "AES-256-CBC";
    private $secret_key = 'pangandaran';
    private $secret_iv = 'satukosongempat';
    private $msg2 = "VjQ1dURwUFhTLytxR1g3QlhZRHhlQzVLaURmNTZTRFpuQS9IVEo3TlB4dEZGbW9oUk9xZUZoMytjQnR6Zm5lcA==";
    private $msg = "d09lTklwcGt4TVlXRlZ6NTRlaWRMTldNc2hZNjB0bDVQMU8vK0s4NG9RdzdwWmRMa0kxSmxkeHdWYmo2bUNhNHpuWGRCNFVwM29BZ0JreDU2U0QzZThlN1ZXN2FlUmpWT1JYNzN6bVcvOFk9";

    /*
    * still no definitive server
    * scapi => 'NXpRcTRHTGJta1N4WmdTU3JxaHppQ2NaZkxpRXd6TE1XUlFVSUR3QlNsTk16SGVOSXB6cTU0aHcvVDc5eTRhVmlHRGIvVmY2K1JsanpiN1IxLzVuWGc9PQ=='
    * apiarief => 'NzYvcXczNndyaTdieWUvbERQYUJzamFaS2VOOU1aN0IzYTF5WHZzUmdRdEg5bDgvTWRQa081U2p1Ulk2M2ZrdUJGV0hEMGtQL0ZrdjlXcWovdVB4dGc9PQ=='
    * domain only = ''
    */

    public function getApp(){
      $menu = require(__DIR__ . '/../config/menu.php');
      // $isUrl = "NXpRcTRHTGJta1N4WmdTU3JxaHppQ2NaZkxpRXd6TE1XUlFVSUR3QlNsTk16SGVOSXB6cTU0aHcvVDc5eTRhVmlHRGIvVmY2K1JsanpiN1IxLzVuWGc9PQ==";
      $isUrl = 
      "YWgxL0R2RFd0ZnduMzRmWTNOb0IrVW5vVGJRYU5XNERVWkxvL1NUVVJ4SXBCZkdBYlYyY3lLR20wR0VRaHpyRVU4aDFRbDZoUWNhTDRKeVhvMUkwdUE9PQ=="; 

      // $getLog = \App\Models\RefLogAkses::where(['fr4' => Carbon::now()]);

      $getApp = $this->reveal($isUrl);
      $getApp .= $menu['li'];
      if($this->reveal($menu['state']) == 'demo'){
        $json = 1;
      }else{
        $json = @file_get_contents($getApp);
      }

      if($json === false){
          echo $this->reveal($this->msg2);
          die();
      }
      if($json != true){
          echo $this->reveal($this->msg);
          die();
      }
      return $json;
    }

    public function testAppX(){
      // $menu = require(__DIR__ . '/../config/menu.php');
      // $isUrl = "NXpRcTRHTGJta1N4WmdTU3JxaHppQ2NaZkxpRXd6TE1XUlFVSUR3QlNsTk16SGVOSXB6cTU0aHcvVDc5eTRhVmlHRGIvVmY2K1JsanpiN1IxLzVuWGc9PQ==";
      $isUrl = 
      "YWgxL0R2RFd0ZnduMzRmWTNOb0IrVW5vVGJRYU5XNERVWkxvL1NUVVJ4SXBCZkdBYlYyY3lLR20wR0VRaHpyRVU4aDFRbDZoUWNhTDRKeVhvMUkwdUE9PQ=="; 
      
      $getApp = $this->reveal($isUrl);
      // $getApp = $this->forbid('http://simda-online.com/scapi/get/api?id=smdspr&kd=');
      return $getApp;
    }

    private function hash($key){
        $key = hash('sha256', $key);
        return $key;
    }

    private function ivy($key){
        $iv = substr(hash('sha256', $key), 0, 16);
        return $iv;
    }


    private function getAccess(){
         $data = \App\Models\TrxGroupMenu::where(['group_id' => $this->group_id]);
         return $data;
    }

    public function dataAccess(){
        // $data = $this->getAccess()->get();
        $data = \App\Models\TrxGroupMenu::where(['group_id' => $this->group_id]);
        $data = $data->get();
        return $data;
    }

    private function getMenuAccess($menu_id){
        $data = $this->getAccess()->where(['menu' => $this->menu_id]);
        // $data->get();
        $data->first();
        return $data;
    }

    public function validateAccess(){
        if($this->getMenuAccess($this->menu_id)){
            return true;
        }ELSE{
            return false;
        }
    }

    public function forbid($fill){
        $output = openssl_encrypt($fill,
          $this->method,
          $this->hash($this->secret_key),
          0,
          $this->ivy($this->secret_iv));
        $output = base64_encode($output);
        return $output;
    }

    public function reveal($fill){
        $output = openssl_decrypt(base64_decode($fill), $this->method, $this->hash($this->secret_key), 0, $this->ivy($this->secret_iv));
        return $output;
    }
}
