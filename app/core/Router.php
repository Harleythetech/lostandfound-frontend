<?php

class Router {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_GET['url'] ?? '/';
        $path = '/' . trim($path, '/');
        
        // Check for exact match
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }
        
        // Check for dynamic routes
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                return call_user_func_array($callback, $matches);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        include __DIR__ . '/../../views/errors/404.php';
    }
}
