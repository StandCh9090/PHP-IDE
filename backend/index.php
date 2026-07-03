<?php
/**
 * PHP-IDE バックエンドエントリーポイント
 * REST API ルーティング
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/controllers/FileController.php';
require_once __DIR__ . '/controllers/TerminalController.php';
require_once __DIR__ . '/controllers/AnalyzerController.php';

try {
    $router = new Router();
    
    // ファイルエンドポイント
    $router->get('/files', 'FileController@getFileTree');
    $router->get('/file', 'FileController@getFile');
    $router->post('/file/create', 'FileController@createFile');
    $router->post('/file/save', 'FileController@saveFile');
    $router->delete('/file', 'FileController@deleteFile');
    $router->post('/folder/create', 'FileController@createFolder');
    $router->put('/file/rename', 'FileController@renameFile');
    
    // ターミナルエンドポイント
    $router->post('/terminal/execute', 'TerminalController@executeCommand');
    
    // コード解析エンドポイント
    $router->post('/analyze/syntax', 'AnalyzerController@analyzeSyntax');
    $router->post('/analyze/lint', 'AnalyzerController@lint');
    $router->post('/analyze/format', 'AnalyzerController@formatCode');
    
    // ルーティング実行
    $router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
