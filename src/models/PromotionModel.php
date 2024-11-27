<?php
class ProductModel {
    private $conn;
    private $tableName = "order_";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->tableName. "WHERE NOW() > start_date  AND NOW() < end_date ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data){
        $query = "INSERT INTO". $this->tableName ."(code_id, name_, start_date, end_date, min_order,maximum_promo,promo_value,init_quantity  )
        VALUES (:code_id, :name_, :start_date, :end_date, :min_order,:maximum_promo,:promo_value,:init_quantity)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code_id', $data['code_id'], PDO::PARAM_INT);
        $stmt->bindParam(':name_', $data['name_'], PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
        $stmt->bindParam(':min_order', $data['min_order'], PDO::PARAM_INT);
        $stmt->bindParam(':maximum_promo', $data['maximum_promo'], PDO::PARAM_INT);
        $stmt->bindParam(':promo_value', $data['promo_value'], PDO::PARAM_STR); 
        $stmt->bindParam(':init_quantity', $data['init_quantity'], PDO::PARAM_INT);
        $stmt->execute();
        $code_id = $pdo->lastInsertId();
        return $code_id
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE order_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}