<?php
/**
 * シンプルなルーター実装
 */
class Router {
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];
    
    /**
     * GETルートを登録
     */
    public function get($path, $controller) {
        $this->routes['GET'][$path] = $controller;
    }
    
    /**
     * POSTルートを登録
     */
    public function post($path, $controller) {
        $this->routes['POST'][$path] = $controller;
    }
    
    /**
     * PUTルートを登録
     */
    public function put($path, $controller) {
        $this->routes['PUT'][$path] = $controller;
    }
    
    /**
     * DELETEルートを登録
     */
    public function delete($path, $controller) {
        $this->routes['DELETE'][$path] = $controller;
    }
    
    /**
     * ルーティング実行
     */
    public function dispatch($method, $path) {
        // パスの正規化
        $path = parse_url($path, PHP_URL_PATH);
        $path = str_replace('/api', '', $path);
        if (empty($path)) $path = '/';
        
        // マッチするルートを探す
        $route = $this->routes[$method][$path] ?? null;
        
        if (!$route) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Route not found']);
            return;
        }
        
        // コントローラーとメソッドを分割
        list($controller, $method) = explode('@', $route);
        
        // コントローラーをインスタンス化して呼び出し
        $controllerClass = '\\' . $controller;
        $instance = new $controllerClass();
        $instance->$method();
    }
}
