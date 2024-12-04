<?php
class OrderModel {
    private $conn;
    private $tableName = "order_";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->tableName;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validateAndCreate($data) {
        if (!$this->validateData($data, true)) {
            throw new Exception('Invalid data');
        }

        $query = "INSERT INTO " . $this->tableName . "
            SET user_id = :user_id,
                total_amount = :total_amount,
                status = :status,
                shipping_address = :shipping_address,
                order_date = :order_date";

        $stmt = $this->conn->prepare($query);
        $this->bindOrderParams($stmt, $data);
        return $stmt->execute();
    }

    public function validateAndUpdate($id, $data) {
        $existing = $this->getById($id);
        if (!$existing) {
            throw new Exception('Order not found');
        }

        if (!$this->validateData($data, false)) {
            throw new Exception('Invalid data');
        }

        $updateData = array_merge($existing, $data);

        $query = "UPDATE " . $this->tableName . " 
            SET user_id = :user_id,
                total_amount = :total_amount,
                status = :status,
                shipping_address = :shipping_address,
                order_date = :order_date
            WHERE order_id = :id";

        $stmt = $this->conn->prepare($query);
        $this->bindOrderParams($stmt, $updateData);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function validateData($data, $isCreate) {
        $rules = [
            'user_id' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'status' => 'required',
            'shipping_address' => 'required',
            'order_date' => 'required'
        ];

        if (!$isCreate) {
            $rules = array_intersect_key($rules, $data);
        }

        return Validator::validate($data, $rules);
    }

    private function bindOrderParams($stmt, $data) {
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":total_amount", $data['total_amount']);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":shipping_address", $data['shipping_address']);
        $stmt->bindParam(":order_date", $data['order_date']);
    }

    public function delete($id) {
        if (!$this->getById($id)) {
            throw new Exception('Order not found');
        }
        $query = "DELETE FROM " . $this->tableName . " WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
