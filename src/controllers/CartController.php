<?php
class CartController {
    private $cartModel;
    private $productModel;
    private $auth;

    public function __construct($db) {
        $this->cartModel = new CartModel($db);
        $this->productModel = new ProductModel($db);
        $this->auth = new Auth();
    }


    public function show() {
        try{
            $role =$this->auth->getRole();
            $id = $this->auth->getId();
            if ($role != "customer"){
                Respone::json(403, ['error' => 'Invalid role'])
            }
        }
        catch (Exception $e){
            Response::json(401, ['error' => $e->getMessage()]);
        }
        try {
            $data = Request::getBody();
            $cart = $this->cartModel->getCartByUserId($id);                 
            if ($cart) {
                Response::json(200, [
                                        'data'=> $cart
                                    ]);

            } 
        }catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function addProduct() {
        try {
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role != "customer"){
                    Respone::json(403, ['error' => 'Invalid role'])
                }
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            $cart_id= $this->cartModel->getCartIDByUserId($id);  
            if(empty($cart)){
                $cart_id=$this->cartModel->createCartForUser($id);
            }
            $data = Request::getBody();
            $product_id = data['product_id'];
            $quantity= data['quantity'];
            $data_product = $this->productModel->getById($product_id);
            if ($quantity > $data_product['quantity']){
                Response::json(409, ['message' => 'Not enough product']);
            }
            $this->cartModel->addProductToCart($cart_id,$product_id,$quantity);
            Response::json(201, ['message' => 'Product added successfully']);

        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }

    public function updateProduct(){
        try{            
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role != "customer"){
                    Respone::json(403, ['error' => 'Invalid role'])
                }
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            $cart_id= $this->cartModel->getCartIDByUserId($id);  
            $data = Request::getBody();
            $product_id = data['product_id'];
            $quantity= data['quantity'];
            $this->cartModel->deleteProductFromCart($cart_id,$product_id);
            Response::json(201, ['message' => 'Product Deleted successfully']);
        }
        catch{
            Response::json(500, ['error' => $e->getMessage()]);
        }

        
    }

    public function removeProduct(){
        try{
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role != "customer"){
                    Respone::json(403, ['error' => 'Invalid role'])
                }
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            $id = Resquest::getPath()
            $this->cartModel


        }
        catch (Exception $e){
            Response::json(401, ['error' => $e->getMessage()]);
        }
    }
  

}
