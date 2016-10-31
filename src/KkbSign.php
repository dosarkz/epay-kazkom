<?php
namespace Dosarkz\EPayKazCom;

class KkbSign {
    // -----------------------------------------------------------------------------------------------
    function load_private_key($filename, $password = NULL){
        $this->ecode=0;
        if(!is_file($filename)){ $this->ecode=4; $this->estatus = "[KEY_FILE_NOT_FOUND]"; return false;};
        $c = file_get_contents($filename);
        if(strlen(trim($password))>0){$prvkey = openssl_get_privatekey($c, $password); $this->parse_errors(openssl_error_string());
        } else {$prvkey = openssl_get_privatekey($c); $this->parse_errors(openssl_error_string());};
        if(is_resource($prvkey)){ $this->private_key = $prvkey; return $c;}
        return false;
    }
    // -----------------------------------------------------------------------------------------------
    // Óñòàíîâêà ôëàãà èíâåðñèè
    function invert(){ $this->invert = 1;}
    // -----------------------------------------------------------------------------------------------
    // Ïðîöåññ èíâåðñèè ñòðîêè
    function reverse($str){	return strrev($str);}
    // -----------------------------------------------------------------------------------------------
    function sign($str){
        if($this->private_key){
            openssl_sign($str, $out, $this->private_key);
            if($this->invert == 1) $out = $this->reverse($out);
            //openssl_free_key($this->private_key);
            return $out;
        };
    }

    function sign64($str){
        return base64_encode($this->sign($str));
    }

    function check_sign($data, $str, $filename){
        if($this->invert == 1)  $str = $this->reverse($str);
        if(!is_file($filename)){ $this->ecode=4; $this->estatus = "[KEY_FILE_NOT_FOUND]"; return 2;};
        $this->pubkey = file_get_contents($filename);
        $pubkeyid = openssl_get_publickey($this->pubkey);
        $this->parse_errors(openssl_error_string());
        if (is_resource($pubkeyid)){
            $result = openssl_verify($data, $str, $pubkeyid);
            $this->parse_errors(openssl_error_string());
            openssl_free_key($pubkeyid);
            return $result;
        };
        return 3;
    }

    function check_sign64($data, $str, $filename){
        return $this->check_sign($data, base64_decode($str), $filename);
    }

    function parse_errors($error){
        // -----===++[Parses error to errorcode and message]++===-----
        /*error:0906D06C - Error reading Certificate. Verify Cert type.
        error:06065064 - Bad decrypt. Verify your Cert password or Cert type.
        error:0906A068 - Bad password read. Maybe empty password.*/
        if (strlen($error)>0){
            if (strpos($error,"error:0906D06C")>0){$this->ecode = 1; $this->estatus = "Error reading Certificate. Verify Cert type.";};
            if (strpos($error,"error:06065064")>0){$this->ecode = 2; $this->estatus = "Bad decrypt. Verify your Cert password or Cert type.";};
            if (strpos($error,"error:0906A068")>0){$this->ecode = 3; $this->estatus = "Bad password read. Maybe empty password.";};
            if ($this->ecode = 0){$this->ecode = 255; $this->estatus = $error;};
        };
    }
}