<?php

namespace App;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\MenuForm;
use DB;
use Session;
use Response;
use Validator;
use Auth;

class Fungsi {
    
    public static function validationErrorsToString($errArray) {
        $valArr = array();
        $errStrFinal ='';
        foreach ($errArray->toArray() as $key => $value) { 
            $errStr = $value[0];
            array_push($valArr, $errStr);
        }
        if(!empty($valArr)){
            $errStrFinal = implode("; ", $valArr);
        }
        return $errStrFinal;
    }

    public static function array_to_xml(array $arr, SimpleXMLElement $xml)
    {
        foreach ($arr as $k => $v) {
            is_array($v)
                ? array_to_xml($v, $xml->addChild($k))
                : $xml->addChild($k, $v);
        }
        return $xml;
    }

    public static function hashPemda($data)
    {
        $xHash=hash('sha256','M4h4th1r4rkh4n4th1ef');
        $result=openssl_encrypt($data,"AES-128-ECB",$xHash);
        $result=base64_encode($result);
        return $result;
    }

    public static function dehashPemda($data)
    {
        $xHash=hash('sha256','M4h4th1r4rkh4n4th1ef');        
        $result=openssl_decrypt(base64_decode($data),"AES-128-ECB",$xHash); 
        return $result;
    }

    public static function checkSumData($dataId)
    {
        $nav = require(base_path().'/config/menu.php');
        $menu = new MenuForm;
        $xPemda = $menu->reveal($nav['li']);
        $xHash=hash('sha256',$xPemda);         
        $data = $dataId;        
        // $hasil=openssl_encrypt($data,"AES-128-ECB",$xHash);
        // $result=base64_encode($hasil);
        $result=bcrypt($data);
        return $result;
    }


}
