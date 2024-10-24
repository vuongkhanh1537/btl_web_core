<?php
class ProductController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new ProductModel($db);
    }

    public function index() {
        try {
            $products = $this->productModel->getAll();
            Response::json(200, ['data' => $products]);
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        try {
            $data = Request::getBody();
            
            if (!Validator::validate($data, [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric'
            ])) {
                Response::json(400, ['error' => 'Invalid data']);
                return;
            }
            
            if ($this->productModel->create($data)) {
                Response::json(201, ['message' => 'Product created successfully']);
            } else {
                Response::json(500, ['error' => 'Failed to create product']);
            }
        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }
}