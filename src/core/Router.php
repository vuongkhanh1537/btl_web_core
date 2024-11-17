<?php
class Router {
    private $routes = [];
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addRoute($method, $path, $controller, $action) {
        // Remove btl_web_core from paths when registering routes
        $path = str_replace('/btl_web_core', '', $path);
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function handle() {
        $method = Request::getMethod();
        $path = Request::getPath();
        
        // Remove btl_web_core and anything before it from request path
        $path = preg_replace('/^.*?btl_web_core/', '', $path);
        
        // Keep existing replacement for backwards compatibility
        $path = str_replace('/btl_web_core', '', $path);
        
        foreach ($this->routes as $route) {
            $params = [];
            if ($route['method'] === $method && $this->matchPath($route['path'], $path, $params)) {
                $controller = new $route['controller']($this->db);
                call_user_func_array([$controller, $route['action']], $params);
                return;
            }
        }
        
        Response::json(404, [
            'error' => 'Route not found',
            'requested_path' => $path,
            'requested_method' => $method
        ]);
    }

    private function matchPath($routePath, $requestPath, &$params) {
        // Normalize paths by trimming slashes
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }
        
        $params = [];
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/^{.+}$/', $routeParts[$i])) {
                $params[] = $requestParts[$i];
            } elseif ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }
        
        return true;
    }
}