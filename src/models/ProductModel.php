<?php
class ProductModel {
    private $conn;
    private $tableName = "products";

    public $id;
    public $name;
    public $price;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->tableName;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->tableName . "
            SET 
                name = :name, 
                price = :price, 
                description = :description";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $data["name"]);
        $stmt->bindParam(":price", $data["price"]);
        $stmt->bindParam(":description", $data["description"]);

        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->tableName . "
                SET
                    name = :name,
                    description = :description,
                    price = :price
                WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":price", $data['price']);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}