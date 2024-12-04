<?php

class CartModel {
    private $conn;
    private $tableName = "cart";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCartByUserId($id){
        $query = "SELECT proudct_id as id, name_ as name, price, category, image_path as image, avg(r.score) as rating, quantity, weight, size, color FROM " . $this->tableName . " c where c.user_id =:id Inner join consisted con on c.cart_id = con.cart_id 
        inner join product p on p.id =con.product_id
        inner join review r on p.id = r.product_id
        group by product
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCartIDByUserId($id){
        $query = "SELECT cart_id as id FROM " . $this->tableName . " c where c.user_id =:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCartForUser($id){
        $query = "INSERT into cart (user_id) Values (:user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $cart_id = $pdo->lastInsertId();
        return $cart_id;
    }
    public function addProductToCart($cart_id,$product_id,$quantity){
        $query = "INSERT into consisted (cart_id,product_id,quantity) Values (:cart_id,:product_id,:quantity)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cart_id", $cart_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->execute();
        return $cart_id;
    }
    public function deleteProductFromCart($cart_id,$product_id){
        $query = "DELETE FROM consisted WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cart_id", $cart_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->execute();
    }

    public function updateProductInCart($cart_id,$product_id){
        $query = "UPDATE consisted SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cart_id", $cart_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->execute();
    }

}
?>