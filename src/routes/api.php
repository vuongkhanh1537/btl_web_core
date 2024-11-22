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
        $this->router->addRoute("GET", "/api/collection/{id}", "ProductController", "getCollection");
        $this->router->addRoute("GET", "/api/products/review/{id}", "ProductController", "getReview");

        // User routes
        $this->router->addRoute("GET", "/api/users", "UserController", "index");
        $this->router->addRoute("GET", "/api/users/{id}", "UserController", "show");
        $this->router->addRoute("POST", "/api/signup", "UserController", "signup");
        $this->router->addRoute("POST", "/api/login", "UserController", "login");
        $this->router->addRoute("PUT", "/api/users/{id}", "UserController", "update");
        $this->router->addRoute("DELETE", "/api/users/{id}", "UserController", "delete");

        // Order routes  
        $this->router->addRoute("GET", "/api/orders", "OrderController", "index");
        $this->router->addRoute("GET", "/api/orders/{id}", "OrderController", "show");
        $this->router->addRoute("POST", "/api/orders", "OrderController", "create"); 
        $this->router->addRoute("PUT", "/api/orders/{id}", "OrderController", "update");
        $this->router->addRoute("DELETE", "/api/orders/{id}", "OrderController", "delete");

        
    }
}