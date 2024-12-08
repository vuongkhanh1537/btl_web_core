<?php
class ProductController {
    private $productModel;
    private $orderModel;
    private $auth;

    public function __construct($db) {
        $this->productModel = new ProductModel($db);
        $this->orderModel = new OrderModel($db);
        $this->auth = new Authorization();
    }

    public function index() {
        try {
            //$this->auth->checkPermission('product', 'read');
            $products = $this->productModel->getAll();
            Response::json(200, [
                'data' => $products
            ]);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id) {
        try {
            //$this->auth->checkPermission('product', 'read');
            $product = $this->productModel->getById($id);
            if ($product) {
                $collection_id=$product['collection_id'];
                $similar_product = $this->productModel->getSimilarProduct($id,$collection_id);
                Response::json(200, [
                    "data" =>$product,
                    "variant" => $similar_product
                ] );
            } else {
                Response::json(404, ['error' => 'Product not found']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        try {
            $data = Request::getBody();
            
            if ($this->productModel->validateAndCreate($data)) {
                Response::json(201, ['message' => 'Product created successfully']);
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

    public function getByCategory($category) {
        try {
            $products = $this->productModel->getByCategory($category);
            Response::json(200, $products);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function getByName($name) {
        try {
            $products = $this->productModel->getByName($name);
            Response::json(200, $products);
                  } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }


    public function getByCategories() {
        try {
            $categories = Request::getQueryParams()['categories'] ?? '';
            $categoryArray = explode(',', $categories);
            
            if (empty($categoryArray)) {
                throw new Exception('No categories provided');
            }
            
            $products = $this->productModel->getByCategories($categoryArray);
            Response::json(200, $products);
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

    public function createReview($id) {
        try {
            $data = Request::getBody();
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role !="customer" ){
                    Response::json(403, ['error' => 'Invalid role']);
                } 
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            
            $date = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
            $data['datetime'] =$date;
            $data['user_id']=$id;
            if(empty($this->orderModel->isUserBuy($id,$data['user_id']))){
                Response::json(400, ['error' => "User do not buy product"]);
            }
            else{
                $this->productModel->createReview($data);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

}