<?php
class PromotionModel {
    private $conn;
    private $table = 'promotion_code';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validateAndCreate($data) {
        $this->validate($data);
        
        $query = "INSERT INTO " . $this->table . " 
                (name_, start_date, end_date, promo_value) 
                VALUES (:name, :start_date, :end_date, :promo_value)";

        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':name' => $data['name_'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':promo_value' => $data['promo_value']
        ]);
    }

    public function validateAndUpdate($id, $data) {
        $this->validate($data);
        
        $query = "UPDATE " . $this->table . " 
                SET name_ = :name, 
                    start_date = :start_date, 
                    end_date = :end_date, 
                    promo_value = :promo_value 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $data[':id'] = $id;
        
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name_'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':promo_value' => $data['promo_value']
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    private function validate($data) {
        $rules = [
            'name_' => 'required|string',
            'start_date' => 'required|datetime',
            'end_date' => 'required|datetime',
            'promo_value' => 'required|numeric'
        ];
        
        return Validator::validate($data, $rules);
    }
}