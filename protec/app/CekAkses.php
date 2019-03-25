<?php

namespace App;

/*
* this is model for Form in GroupAkses menu.
* This form didn't entitled to any table in database
* This form just contain somelogic to check are any group has any acces to certain module
* --hoaaah
*/

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Carbon\Carbon;
use \App\MenuForm;

class CekAkses extends MenuForm
{

    // $group_id = Auth::user()->group_id;
    public $menu;

    // gather all akses
    protected function akses(){       
        return \App\Models\TrxGroupMenu::where(['group_id' => Auth::user()->group_id]);
    }    

    // cek certain menu for akses
    public function get($menu){
        // code goes here
        $cek = $this->akses()->where('menu', 'LIKE', "$menu%")->first();
        if($cek['menu'] != NULL){
            return true;
        }else{
            return false;
        }
    }

    // cek fungsi CRUD-Posting
    public function get_created($menu){
        // code goes here
        $cek = $this->akses()->where('menu', '=', "$menu%")->first();
        if(left($cek['menu'],2) == 1){
            return true;
        }else{
            return false;
        }
    }
    
    public function get_read($menu){
        // code goes here
        $cek = $this->akses()->where('menu', '=', "$menu%")->first();
        if(left($cek['menu'],2) == 1){
            return true;
        }else{
            return false;
        }
    }

    public function get_updated($menu){
        // code goes here
        $cek = $this->akses()->where('menu', '=', "$menu%")->first();
        if(left($cek['menu'],2) == 1){
            return true;
        }else{
            return false;
        }
    }

    public function get_deleted($menu){
        // code goes here
        $cek = $this->akses()->where('menu', '=', "$menu%")->first();
        if(left($cek['menu'],2) == 1){
            return true;
        }else{
            return false;
        }
    }

    public function get_posting($menu){
        // code goes here
        $cek = $this->akses()->where('menu', '=', "$menu%")->first();
        if(left($cek['menu'],2) == 1){
            return true;
        }else{
            return false;
        }
    }

    public function getMulti($param)
    {
        /* getMulti use to check if any menu id give true result.
        * This method same as get() method
        * This method will give user ability to find any true result in user-menu-id
        * If any menu give true result, this method will return true.
        * Example code : $this->getMulti([101, 102, 103, 104, 105, 106])
        * Be wise when using this method, too many menu_id may slow down the application
        */
        $result = false;
        foreach($param as $menu){
            $get = $this->get($menu);
            if($get == true) $result = true;
        }
        return $result;
    }

    private function getLog(){
         $data = \App\Models\RefLogAkses::where(['fr4' => Carbon::now()]);
         return $data;
    }
}