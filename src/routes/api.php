<?php
class Routes {
    private Router $router;

    public function __construct($router) {
        $this->router = $router;
        $this->defineRoutes();
    }

    private function defineRoutes() {
        // $this->router->addRoute("GET", "/btl_web_core/api/products", "ProductController", "index");
        // $this->router->addRoute("POST", "/btl_web_core/api/products",  "ProductController", "create");
        $this->router->addRoute("POST", "/btl_web_core/api/login",  "UserController", "login");
        $this->router->addRoute("POST", "/btl_web_core/api/signup",  "UserController", "createCustomer");
    }
}