<?php

class App {
    private $router;
    private $db;

    public function __construct() {
        $this->initializeDatabase();
        $this->initializeRouter();
        $this->loadRoutes();
    }

    public function run() {
        $this->setHeaders();
        $this->router->handle();
    }

    public function getDatabase() {
        return $this->db;
    }

    private function initializeDatabase() {
        $database = new Database();
        $this ->db = $database->getConnection();
    }
    
    private function initializeRouter() {
        $this->router = new Router($this->db);
    }
    
    private function loadRoutes() {
        new Routes($this->router);
    }

    private function setHeaders() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            exit();
        }
    }
}