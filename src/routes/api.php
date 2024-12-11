<?php
class Routes {
    private Router $router;

    public function __construct($router) {
        $this->router = $router;
        $this->defineRoutes();
    }

    private function defineRoutes() {
        // Product routes
        $this->router->addRoute("GET", "/api/products", "ProductController", "index");
        $this->router->addRoute("GET", "/api/products/{id}", "ProductController", "show"); 
        $this->router->addRoute("POST", "/api/products", "ProductController", "create");
        $this->router->addRoute("PUT", "/api/products/{id}", "ProductController", "update");
        $this->router->addRoute("DELETE", "/api/products/{id}", "ProductController", "delete");
        $this->router->addRoute("GET", "/api/products/name/{name}", "ProductController", "getByName");
        $this->router->addRoute("GET", "/api/products/category/{category}", "ProductController", "getByCategory");
        $this->router->addRoute("GET", "/api/products/categories", "ProductController", "getByCategories");
        $this->router->addRoute("POST", "/api/reviews", "ProductController", "createReview");

        // User routes
        $this->router->addRoute("GET", "/api/users", "UserController", "index");
        $this->router->addRoute("GET", "/api/users/{id}", "UserController", "show");
        $this->router->addRoute("GET", "/api/customers", "UserController", "getAllCustomers");
        $this->router->addRoute("POST", "/api/signup", "UserController", "signup");
        $this->router->addRoute("POST", "/api/login", "UserController", "login");
        $this->router->addRoute("PUT", "/api/users", "UserController", "update");
        $this->router->addRoute("DELETE", "/api/users/{id}", "UserController", "delete");

        // Order routes  
        $this->router->addRoute("GET", "/api/orders", "OrderController", "index");
        $this->router->addRoute("GET", "/api/orders/{id}", "OrderController", "show");
        $this->router->addRoute("GET", "/api/orders/{id}/details", "OrderController", "showDetails");
        $this->router->addRoute("POST", "/api/orders", "OrderController", "create"); 
        $this->router->addRoute("PUT", "/api/orders/{id}", "OrderController", "update");
        $this->router->addRoute("DELETE", "/api/orders/{id}", "OrderController", "delete");

      // Collection routes
        $this->router->addRoute("GET", "/api/collections", "CollectionController", "index");
        $this->router->addRoute("GET", "/api/collections/{id}", "CollectionController", "show");
        $this->router->addRoute("POST", "/api/collections", "CollectionController", "create");
        $this->router->addRoute("PUT", "/api/collections/{id}", "CollectionController", "update");
        $this->router->addRoute("DELETE", "/api/collections/{id}", "CollectionController", "delete");


        // Promotion routes
        $this->router->addRoute("GET", "/api/promotions", "PromotionController", "index");
        $this->router->addRoute("POST", "/api/promotions", "PromotionController", "create"); 
        $this->router->addRoute("PUT", "/api/promotions/{id}", "PromotionController", "update");
        $this->router->addRoute("DELETE", "/api/promotions/{id}", "PromotionController", "delete");



        // Cart routes
        $this->router->addRoute("GET", "/api/cart", "CartController", "show");
        $this->router->addRoute("POST", "/api/cart", "CartController", "addProduct");
        $this->router->addRoute("PUT", "/api/cart/{id}", "CartController", "updateProduct");
        $this->router->addRoute("DELETE", "/api/cart/{id}", "CartController", "removeProduct");
       
        // Auth routes
        $this->router->addRoute("POST", "/api/login", "AuthController", "login");

        // Dashboard routes
        $this->router->addRoute("GET", "/api/revenue", "DashboardController", "getTotalRevenue");
        $this->router->addRoute("GET", "/api/dashboard/top-selling", "DashboardController", "getTopSellingProducts");
        $this->router->addRoute("GET", "/api/dashboard/category", "DashboardController", "getSalesByCategory");
        $this->router->addRoute("GET", "/api/dashboard/count", "DashboardController", "getTotalOrderCount");
        $this->router->addRoute("GET", "/api/dashboard/orders/completed", "DashboardController", "getTotalCompletedOrders");
        $this->router->addRoute("GET", "/api/dashboard/revenue/monthly", "DashboardController", "getMonthlyRevenue");
        $this->router->addRoute("PUT", "/api/dashboard/orders/{id}", "DashboardController", "updateOrderStatus");
    }
}