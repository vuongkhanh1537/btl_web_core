<?php
class Auth {
    private $auth;
    private static $permissions = [
        'product' => [
            'admin' => ['create', 'read', 'update', 'delete'],
            'user' => ['read']
        ],
        'order' => [
            'admin' => ['create', 'read', 'update', 'delete'], 
            'user' => ['create', 'read', 'update']
        ],
        // Add other resources permissions here
    ];

    public function __construct() {
        $this->auth = new Authorization();
    }

    public function checkPermission($resource, $action) {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new Exception('Authorization token missing');
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $payload = $this->auth->decode($token);
        $role = $payload['role'] ?? null;
        
        if (!$this->hasPermission($role, $resource, $action)) {
            throw new Exception('Insufficient permissions');
        }

        return true;
    }

    private function hasPermission($role, $resource, $action) {
        return isset(self::$permissions[$resource][$role]) && 
               in_array($action, self::$permissions[$resource][$role]);
    }
}