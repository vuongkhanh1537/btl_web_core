<?php
class CartController {
    private $cartModel;
    private $productModel;
    private $auth;

    public function __construct($db) {
        $this->cartModel = new CartModel($db);
        $this->productModel = new ProductModel($db);
        $this->auth = new Authorization ();
    }


    public function show() {
        try{
            $role =$this->auth->getRole();
            $id = $this->auth->getId();
            if ($role != "customer"){
                Respone::json(403, ['error' => 'Invalid role']);
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
                    Respone::json(403, ['error' => 'Invalid role']);
                }
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            $cart_id= $this->cartModel->getCartIDByUserId($id); 
            if(empty($cart_id)){
                $cart_id=$this->cartModel->createCartForUser($id);
            }
            else{
                $cart_id = $cart_id[0]['cart_id'];
            }
  

            
            $data = Request::getBody();
            $product_id = $data['product_id'];
            $quantity= $data['quantity'];
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


    public function updateProduct($product_id) {
        try {
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role != "customer"){
                    Respone::json(403, ['error' => 'Invalid role']);
                }
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            $cart_id= $this->cartModel->getCartIDByUserId($id);  
            if(empty($cart_id)){
                $cart_id=$this->cartModel->createCartForUser($id);
            }
            else{
                $cart_id = $cart_id[0]['cart_id'];
            }   
            $data = Request::getBody();
            $quantity= $data['quantity'];
            $data_product = $this->productModel->getById($product_id);
            if ($quantity > $data_product['quantity']){
                Response::json(409, ['message' => 'Not enough product']);
            }
            $this->cartModel->updateProductInCart($cart_id,$product_id,$quantity);
            Response::json(201, ['message' => 'Product updated successfully']);

        } catch (Exception $e) {
            Response::json(500, ['error' => $e->getMessage()]);
        }
    }


    public function removeProduct($product_id){
        try{            
            try{
                $role =$this->auth->getRole();
                $id = $this->auth->getId();
                if ($role != "customer"){
                    Respone::json(403, ['error' => 'Invalid role']);
                }
            }
            catch (Exception $e){
                Response::json(401, ['error' => $e->getMessage()]);
            }
            $cart_id= $this->cartModel->getCartIDByUserId($id);  
            $cart_id= $this->cartModel->getCartIDByUserId($id);  
            if(empty($cart_id)){
                Response::json(409, ['error' => 'Cart is not existed']);
            }
            else{
                $cart_id = $cart_id[0]['cart_id'];
            }   
            $data = Request::getBody();
            $this->cartModel->deleteProductFromCart($cart_id,$product_id);
            Response::json(201, ['message' => 'Product Deleted successfully']);
        }
        catch (Exception $e){
            Response::json(500, ['error' => $e->getMessage()]);
        }

        
    }

}
