<?php
class ProductModel {
    private $conn;
    private $tableName = "product";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT p.product_id as id, name_ as name, price, category, collection_id, image_path as image, avg(r.score) as rating   FROM " . $this->tableName . " p inner join review r on p.product_id = r.product_id group by r.product_id
        UNION 
        SELECT p.product_id as id, name_ as name, price, category, collection_id, image_path as image, 0.0 as rating  FROM product p";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT p.product_id as id, name_ as name, price, color, brand, description_ as description, weight_ as weight, category, image_path as image, quantity,collection_id, avg(r.score) as rating   FROM " . $this->tableName . " p Inner join review r on p.product_id = r.product_id  WHERE p.product_id = ? group by r.product_id
        UNION
        SELECT p.product_id as id, name_ as name, price, color, brand, description_ as description, weight_ as weight, category, image_path as image, quantity,collection_id, 0.0 as rating   FROM " . $this->tableName . " p  WHERE p.product_id = ? 
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $id);
        $stmt->execute();
        $main_product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $main_product;
    }



    public function validateAndCreate($data) {
        if (!$this->validateData($data, true)) {
            throw new Exception('Invalid data');
        }

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

    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE category = :category";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByName($name) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE name_ LIKE :name";
        $stmt = $this->conn->prepare($query);
        $searchName = "%" . $name . "%";
        $stmt->bindParam(":name", $searchName);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategories($categories) {
        $placeholders = str_repeat('?,', count($categories) - 1) . '?';
        $query = "SELECT * FROM " . $this->tableName . " WHERE category IN ($placeholders)";
        $stmt = $this->conn->prepare($query);
        
        // Bind each category to its placeholder
        foreach ($categories as $key => $category) {
            $stmt->bindValue($key + 1, $category);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSimilarProduct($id, $CollectionId){
        $query = "SELECT proudct_id as id, name_ as name, price, category, image_path as image, description,weight avg(r.score) as rating   FROM " . $this->tableName . " p 
        Inner join review r on p.product_id = r.product_id group by product_id where p.collection_id = :collection_id and p.product_id = :product_id";
        $query = "SELECT p.* FROM ". $this->tableName . " p 
        inner join collection_ c on c.collection_id = p.collection_id
        where c.collection_id = :collection_id AND product_id != :id" ;
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':collection_id', $CollectionId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
    public function updateQuantityProduct($product_id, $quantity){
        $query = "UPDATE into consisted (cart_id,product_id,quantity) Values (:cart_id,:product_id,:quantity)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cart_id", $cart_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->execute();
        return $cart_id;
    }
    

}