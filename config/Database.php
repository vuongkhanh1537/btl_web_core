<?php

class Database {
    private $host ;
    private $db_name ;
    private $username ;
    private $password ;
    private $port;
    public $conn;

    public function __construct() {
        $this->host = "113.161.170.162";
        $this->db_name= "SHOE_SHOP";
        $this->username= "root";
        $this->password= "ltw@241";
        $this->port="34575";
        $this->conn = null;

    }

    public function getConnection() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=". $this->port.";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}