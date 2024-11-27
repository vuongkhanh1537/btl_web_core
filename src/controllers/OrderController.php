<?php
class OrderController {
    private $orderModel;
    private $auth;

    public function __construct($db) {
        $this->orderModel = new OrderModel($db);
        $this->auth = new Auth();
    }

    public function index() {
        try {
            //$this->auth->checkPermission('order', 'read');
            $orders = $this->orderModel->getAll();
            Response::json(200, $orders);

        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id) {
        try {
            //$this->auth->checkPermission('order', 'read');
            $order = $this->orderModel->getById($id);
            if ($order) {
                Response::json(200, $order);
            } else {
                Response::json(404, ['error' => 'Order not found']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        try {
            //$this->auth->checkPermission('order', 'create');
            $data = Request::getBody();
            
            if ($this->orderModel->validateAndCreate($data)) {
                Response::json(201, ['message' => 'Order created successfully']);
            }
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


    public function getCollection($CollectionId) {
        try {
            //$this->auth->checkPermission('product', 'delete');
            $data=$this->productModel->getProductInCollection($CollectionId);
            if ($data) {
                Response::json(200, $data);
            } else {
                Response::json(404, ['error' => 'Collection not found']);

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
        }catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
}

    public function getReview($id) {
        try {
            $data=$this->productModel->getReview($id);
            if ($data) {
                Response::json(200, $data);
            } else {
                Response::json(404, ['error' => 'Collection not found']);

            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }
}

