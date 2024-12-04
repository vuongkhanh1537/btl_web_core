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

    public function getDetails($id) {
        try {
            // First verify order exists
            $order = $this->getById($id);
            if (!$order) {
                throw new Exception("Order with ID $id not found");
            }
        
            // Get order details including contained products and user details
            $query = "SELECT o.*, 
                    c.product_id, c.quantity, c.price,
                    p.name_ as product_name, p.color, p.brand,
                    u.user_id, u.name_ as user_name, u.email, u.role_, 
                    u.gender, u.birthday
                FROM " . $this->tableName . " o
                INNER JOIN contain c ON o.order_id = c.order_id 
                INNER JOIN product p ON c.product_id = p.product_id
                INNER JOIN user u ON o.user_id = u.user_id
                WHERE o.order_id = ?";
        
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($details)) {
                throw new Exception("No order details found for order ID $id");
            }
    
            // Format response
            $orderInfo = [
                'order_id' => $details[0]['order_id'],
                'order_time' => $details[0]['order_time'],
                'shipment_time' => $details[0]['shipment_time'],
                'ship_fee' => $details[0]['ship_fee'],
                'payment_status' => $details[0]['payment_status'], 
                'total_payment' => $details[0]['total_payment'],
                'payment_method' => $details[0]['payment_method'],
                'payment_time' => $details[0]['payment_time'],
                'status_' => $details[0]['status_'],
                'address_' => $details[0]['address_'],
                'user' => [
                    'id' => $details[0]['user_id'],
                    'name' => $details[0]['user_name'],
                    'email' => $details[0]['email'],
                    'role' => $details[0]['role_'],
                    'gender' => $details[0]['gender'], 
                    'birthday' => $details[0]['birthday']
                ],
                'promotion_code_id' => $details[0]['promotion_code_id'],
                'discount' => $details[0]['discount'],
                'products' => []
            ];
    
            foreach ($details as $detail) {
                $orderInfo['products'][] = [
                    'product_id' => $detail['product_id'],
                    'product_name' => $detail['product_name'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'color' => $detail['color'],
                    'brand' => $detail['brand']
                ];
            }
    
            return $orderInfo;
    
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
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
