<?php
class CollectionModel {
    private $conn;
    private $tableName = "collection_";

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
        $query = "SELECT * FROM " . $this->tableName . " WHERE collection_id = ?";
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
            SET name_ = :name,
                description_ = :description,
                created_date = :created_date,
                status = :status";

        $stmt = $this->conn->prepare($query);
        $this->bindCollectionParams($stmt, $data);
        return $stmt->execute();
    }

    public function validateAndUpdate($id, $data) {
        $existing = $this->getById($id);
        if (!$existing) {
            throw new Exception('Collection not found');
        }

        if (!$this->validateData($data, false)) {
            throw new Exception('Invalid data');
        }

        $updateData = array_merge($existing, $data);

        $query = "UPDATE " . $this->tableName . " 
            SET name_ = :name,
                description_ = :description,
                created_date = :created_date,
                status = :status
            WHERE collection_id = :id";

        $stmt = $this->conn->prepare($query);
        $this->bindCollectionParams($stmt, $updateData);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function validateData($data, $isCreate) {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'created_date' => 'required',
            'status' => 'required'
        ];

        if (!$isCreate) {
            $rules = array_intersect_key($rules, $data);
        }

        return Validator::validate($data, $rules);
    }

    private function bindCollectionParams($stmt, $data) {
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":created_date", $data['created_date']);
        $stmt->bindParam(":status", $data['status']);
    }

    public function delete($id) {
        if (!$this->getById($id)) {
            throw new Exception('Collection not found');
        }
        $query = "DELETE FROM " . $this->tableName . " WHERE collection_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
