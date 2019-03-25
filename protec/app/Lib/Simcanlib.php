<?php
// Buat namespace sesuai folder
namespace App\Lib;
 
class Simcanlib {

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
    

}