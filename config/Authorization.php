<?php
class Authorization {
    private $key ;
    private $algorithm = 'HS256';
    private $secret ='BKU';

    public function __construct() {
        $this->key = "Assignment_Web_Sem_241";
        $this->algorithm = "HS256";
        $this->secret="BKU";
    }

    public function encode($payload) {
        $header = json_encode(['alg' => $this->algorithm , 'typ' => 'JWT']);
        
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);


        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function decode($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new Exception('Invalid JWT format');
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;


        $header = json_decode($this->base64UrlDecode($base64UrlHeader), true);
        $payload = json_decode($this->base64UrlDecode($base64UrlPayload), true);
        $signature = $this->base64UrlDecode($base64UrlSignature);


        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        if (!hash_equals($signature, $expectedSignature)) {
            throw new Exception('Invalid signature');
        }

        return $payload;
    }

    private function base64UrlEncode($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }


    private function base64UrlDecode($data) {
        $base64 = str_replace(['-', '_'], ['+', '/'], $data);
        return base64_decode($base64);
    }

    public function getRole() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new Exception('Authorization token missing');
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $payload = $this->decode($token);
        $role = $payload['role'] ?? null;

        return $role;
    }

    public function getId(){
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new Exception('Authorization token missing');
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $payload = $this->decode($token);
        $role = $payload['role'] ?? null;

        return $role;
    }
}
?>