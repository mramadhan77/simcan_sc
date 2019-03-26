<?php
namespace App\Listeners;

use Session;
use App\Classes\Helper;

class ClearSessionAfterUserLogout{
    
    public function handle(Logout $event){
        Session::flush();
        Session::set('configuration', NULL);
        Helper::unloadConfiguration();
        return redirect('/');
    }
}
?>