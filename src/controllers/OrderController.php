<?php
class OrderController {
    private $orderModel;
    private $cartModel;
    private $auth;

    public function __construct($db) {
        $this->orderModel = new OrderModel($db);
        $this->cartModel = new CartModel($db);
        $this->auth = new Authorization();
    }

    public function index() {
        try {
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role !="customer" && $role != "manager" ){
                    Response::json(403, ['error' => 'Invalid role']);
                } 
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            if ($role =="customer"){
                $order = $this->orderModel->getAllByUserId($id);
                Response::json(200, ["orders"=>$order] );
            } 
            else{
                $orders = $this->orderModel->getAll();
                Response::json(200, $orders);
            }
        }  catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id) {
        try {
            //$this->auth->checkPermission('order', 'read');
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role !="customer" && $role != "manager" ){
                    Response::json(403, ['error' => 'Invalid role']);
                } 
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }

            $order = $this->orderModel->getById($id);
            if ($order) {
                if($role=="customer"){
                    if ($order['user_id'] != $id){
                        Response::json(403, ['error' => 'Invalid role']);
                    }
                }
                Response::json(200, $order);
            } else {
                
                Response::json(404, ['error' => 'Order not found']);
            }
                
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function showDetails($id) {
        try {
            //$this->auth->checkPermission('order', 'read');
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role !="customer" && $role != "manager" ){
                    Response::json(403, ['error' => 'Invalid role']);
                } 
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            $details = $this->orderModel->getDetails($id);
            if($role=="customer"){
                if ($details['user_id'] != $id){
                    Response::json(404, ['error' => 'User not ơn that order']);
                }
            }
            
        
            if ($details) {
                Response::json(200, $details);
            } else {
                Response::json(404, ['error' => 'Order details not found']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        try {
            $data = Request::getBody();
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role !="customer") {
                    Response::json(403, ['error' => 'Invalid role']);
                } 
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            
            $cart= $this->cartModel->getCartByUserId($id);
            if (!empty($cart)) {
                $totalCost = 0;
                foreach ($cart as $item) {
                    if ($item['quantity'] > $item['product_quantity']) {
                        Response::json(400, [
                            'error' => 'Insufficient stock for product: ' . $item['name'],
                            'product_id' => $item['id'],
                            'available_quantity' => $item['product_quantity'],
                            'requested_quantity' => $item['quantity']
                        ]);
                        return; 
                    }
                    $totalCost += $item['quantity'] * $item['price'];
                }
                $data['items']=$cart;
                $data['total_payment']=$totalCost;
            }
            else{
                Response::json(404, ['error' => 'Cart do not exist']);
            }
            $data['items'] =$cart;
            $data['user_id'] = $this->auth->getId();
            $order_id=$this->orderModel->validateAndCreate($data);
            $this->cartModel->deleteCart($id);
            Response::json(201, ['message' => 'Order created successfully', 'data'=>['id'=>$order_id]]);
            
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update($id) {
        try {
            //$this->auth->checkPermission('order', 'update');
            $data = Request::getBody();
            
            if ($this->orderModel->validateAndUpdate($id, $data)) {
                Response::json(200, ['message' => 'Order updated successfully']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete($id) {
        try {
            //$this->auth->checkPermission('order', 'delete');
            if ($this->orderModel->delete($id)) {
                Response::json(200, ['message' => 'Order deleted successfully']);
            } else {
                Response::json(500, ['error' => 'Failed to delete order']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }
}
