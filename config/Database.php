<?php

class Database {
    private $host ;
    private $db_name ;
    private $username ;
    private $password ;
    public $conn;

    public function __construct() {
        $this->host = "localhost";
        $this->db_name= "assign_db";
        $this->username= "root";
        $this->password= "";
        $this->conn = null;

    }

    public function getConnection() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}