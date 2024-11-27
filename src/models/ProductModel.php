<?php
class ProductModel {
    private $conn;
    private $tableName = "product";

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
        $query = "SELECT * FROM " . $this->tableName . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validateAndCreate($data) {
        // Validate data
        if (!$this->validateData($data, true)) {
            throw new Exception('Invalid data');
        }

        // Create product
        $query = "INSERT INTO " . $this->tableName . "
            SET name_ = :name,
                price = :price,
                description_ = :description,
                color = :color,
                brand = :brand,
                weight_ = :weight,
                size_ = :size,
                quantity = :quantity,
                category = :category";

        $stmt = $this->conn->prepare($query);
        $this->bindProductParams($stmt, $data);
        return $stmt->execute();
    }

    public function validateAndUpdate($id, $data) {
        // Check if product exists
        $existing = $this->getById($id);
        if (!$existing) {
            throw new Exception('Product not found');
        }

        // Validate provided fields
        if (!$this->validateData($data, false)) {
            throw new Exception('Invalid data');
        }

        // Merge with existing data
        $updateData = array_merge($existing, $data);

        // Update product
        $query = "UPDATE " . $this->tableName . " 
            SET name_ = :name,
                price = :price,
                description_ = :description,
                color = :color,
                brand = :brand,
                weight_ = :weight,
                size_ = :size,
                quantity = :quantity,
                category = :category
            WHERE product_id = :id";

        $stmt = $this->conn->prepare($query);
        $this->bindProductParams($stmt, $updateData);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function validateData($data, $isCreate) {
        $rules = [
            'name' => 'required',
            'price' => 'required|numeric',
            'color' => 'required',
            'brand' => 'required',
            'description' => 'required',
            'weight' => 'required|numeric',
            'size' => 'required|numeric',
            'quantity' => 'required|numeric',
            'category' => 'required'
        ];

        if (!$isCreate) {
            // For updates, only validate provided fields
            $rules = array_intersect_key($rules, $data);
        }

        return Validator::validate($data, $rules);
    }

    private function bindProductParams($stmt, $data) {
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":price", $data['price']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":color", $data['color']);
        $stmt->bindParam(":brand", $data['brand']);
        $stmt->bindParam(":weight", $data['weight']);
        $stmt->bindParam(":size", $data['size']);
        $stmt->bindParam(":quantity", $data['quantity']);
        $stmt->bindParam(":category", $data['category']);
    }

    public function delete($id) {
        if (!$this->getById($id)) {
            throw new Exception('Product not found');
        }
        $query = "DELETE FROM " . $this->tableName . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function getProductInCollection($CollectionId){
        $query = "SELECT p.* FROM ". $this->tableName . " p 
        inner join collection_ c on c.collection_id = p.collection_id
        where c.collection_id = :collection_id" ;
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':collection_id', $CollectionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getReview($id){
        if (!$this->getById($id)) {
            throw new Exception('Product not found');
        }
        $query = "SELECT r.*, u.name_  FROM review r
            inner join user u on u.user_id = r.reviewer_id
            where r.product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}