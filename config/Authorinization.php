<?php
use Firebase\JWT\JWT;
class Authorinization {
    private $key ;
    private $algorithm = 'HS256';

    public static function __construct() {
        $this->key = "Assignment_Web_Sem_241";
    }

    public function authorize($userrole, $role) {
        $roledecoded = JWT::decode( $userrole, $this->key, $this->algorithm);
        if ($roledecoded == $role) {
            return true; 
        } else {
            return false;
        }
    }
    public function encode($role) {
        return JWT::encode($role,$this->key,$this->algorithm);
    }

}