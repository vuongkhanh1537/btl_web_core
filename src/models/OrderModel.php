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

    public function getAllByUserId($id) {
        $ordersQuery = "
        SELECT order_id, status_ AS status, total_payment AS total_price
        FROM order_;
        where user_id=:user_id
    ";
        $stmt = $this->conn->prepare($ordersQuery);
        $stmt->bindParam(":user_id", $id);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $placeholders = implode(',', array_fill(0, count($orders), '?'));
        if (!empty($orders)){
            $itemsQuery = "
            SELECT order_id, product_id, quantity, price
            FROM contain
            WHERE order_id IN ($placeholders);
        ";

            $stmt = $this->conn->prepare($itemsQuery);
            $stmt->execute($orders);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
       

        $itemsByOrderId = [];
        foreach ($items as $item) {
            $itemsByOrderId[$item['order_id']][] = [
                'product_id' => (int) $item['product_id'],
                'quantity'   => (int) $item['quantity'],
                'price'      => (int) $item['price'],
            ];
        }
        $result = ['orders' => []];
        foreach ($orders as $order) {
            $result['orders'][] = [
                'order_id'    => (int) $order['order_id'],
                'status'      => $order['status'],
                'items'       => $itemsByOrderId[$order['order_id']] ?? [],
                'total_price' => (int) $order['total_price'],
            ];
        }
            return $result;
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
        
        $data['payment_status'] = "Not Completed";
        $data['status_']="Shipping";
        $query = "SELECT promo_value, start_date, end_date FROM promotions WHERE code_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(1, $data['discount_code']); // Bind the promo ID as an integer
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $promo = $result->fetchAll(PDO::FETCH_ASSOC);
            $currentDate = new DateTime();
            $startDate = new DateTime($promo['start_date']);
            $endDate = new DateTime($promo['end_date']);
    
            if ($currentDate >= $startDate && $currentDate <= $endDate) {
                $data['total_payment']=$data['total_payment']*(1-$promo['promo_value']);
                $data['discount']=-$promo['promo_value'];
            } else {
                throw new Exception('Invalid promotioin code');
            }
        } 
        if (!$this->validateData($data, true)) {
            throw new Exception('Invalid data');
        }
        $query = "INSERT INTO order_ 
        SET 
            order_time = :order_time,
            shipment_time = :shipment_time,
            ship_fee = :ship_fee,
            payment_status = :payment_status,
            total_payment = :total_payment,
            payment_method = :payment_method,
            payment_time = :payment_time,
            status_ = :status_,
            address_ = :address_,
            user_id = :user_id,
            promotion_code_id = :promotion_code_id,
            discount = :discount";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_time', $data['order_time']);
        $stmt->bindParam(':ship_fee', $data['delivery_fee'], PDO::PARAM_STR);
        $stmt->bindParam(':payment_status', $data['payment_status'], PDO::PARAM_STR);
        $stmt->bindParam(':total_payment', $data['total_payment'], PDO::PARAM_INT);
        $stmt->bindParam(':payment_method', $data['payment_method'], PDO::PARAM_STR);
        $stmt->bindParam(':status_', $data['status'], PDO::PARAM_STR);
        $stmt->bindParam(':address_', $data['delivery_address']['address'], PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':promotion_code_id', $data['discount_code'], PDO::PARAM_INT);
        $stmt->bindParam(':discount', $data['discount'], PDO::PARAM_INT);
        $this->bindOrderParams($stmt, $data);
        $stmt->execute();
        $orderId = $this->conn->lastInsertId();
        $items = $data['items']; 

        foreach ($items as $item) {
            $queryContain = "INSERT INTO contain (order_id, product_id, quantity, price) 
                            VALUES (:order_id, :product_id, :quantity, :price)";
            $stmtContain = $this->conn->prepare($queryContain);
            $stmtContain->bindParam(':order_id', $orderId);
            $stmtContain->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
            $stmtContain->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmtContain->bindParam(':price', $item['price'], PDO::PARAM_INT);
            $stmtContain->execute();

            $queryUpdateProduct = "UPDATE product 
                                SET quantity = quantity - :sold_quantity 
                                WHERE product_id = :product_id";
            $stmtUpdate = $this->conn->prepare($queryUpdateProduct);
            $stmtUpdate->bindParam(':sold_quantity', $item['quantity'], PDO::PARAM_INT);
            $stmtUpdate->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
            $stmtUpdate->execute();
        }
        return $orderId;
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
            'order_time'         => 'required|datetime',
            'shipment_time'      => 'datetime',
            'ship_fee'           => 'required|numeric',
            'payment_status'     => 'required',
            'total_payment'      => 'required|int',
            'payment_method'     => 'required|max',
            'payment_time'       => 'datetime',
            'status_'            => 'required',
            'address_'           => 'required|max',
            'user_id'            => 'required|int',
            'promotion_code_id'  => 'int',
            'discount'           => 'int'
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
    public function isUserBuy($user_id,$product_id){
        $query = "Select FROM order_ 
        Inner join contain c on c.order_id = order_.order_id
        WHERE user_id = :user_id and product_id= :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->execute();
            
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $details;
    }
}
