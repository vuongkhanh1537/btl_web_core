<?php
class ProductController {
    private $productModel;
    private $auth;

    public function __construct($db) {
        $this->productModel = new ProductModel($db);
        $this->auth = new Auth();
    }

    public function index() {
        try {
            //$this->auth->checkPermission('product', 'read');
            $products = $this->productModel->getAll();
            Response::json(200, $products);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function show($id) {
        try {
            //$this->auth->checkPermission('product', 'read');
            $product = $this->productModel->getById($id);
            if ($product) {
                Response::json(200, $product);
            } else {
                Response::json(404, ['error' => 'Product not found']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        try {
            //$this->auth->checkPermission('product', 'create');
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
}