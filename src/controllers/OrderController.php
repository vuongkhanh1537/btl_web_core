<?php
class OrderController {
    private $orderModel;
    private $auth;

    public function __construct($db) {
        $this->orderModel = new OrderModel($db);
        $this->auth = new Auth();
    }

    public function index() {
        

    }

    public function show($id) {
        
    }

    public function create() {
        try {
            $data = Request::getBody();
            if()
            $data['order_id'] = $this->orderModel->createOrder($data);
            $this->orderModel->addProductToOrder($data)
            if ($this->productModel->validateAndCreate($data)) {
                Response::json(201, ['message' => 'Order created successfully',
                                        'order_id'=> $data['id']
                                    ]);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function update($id) {
        try {
            //$this->auth->checkPermission('product', 'update');
            $data = Request::getBody();
            
            if ($this->productModel->validateAndUpdate($id, $data)) {
                Response::json(200, ['message' => 'Product updated successfully']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function delete($id) {
        try {
            //$this->auth->checkPermission('product', 'delete');
            if ($this->productModel->delete($id)) {
                Response::json(200, ['message' => 'Product deleted successfully']);
            } else {
                Response::json(500, ['error' => 'Failed to delete product']);
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