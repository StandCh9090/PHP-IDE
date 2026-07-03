<?php
/**
 * LSP API エンドポイント統統
 */
require_once __DIR__ . '/LanguageServer.php';

class LSPController {
    private $server;
    
    public function __construct() {
        $this->server = new LanguageServer();
    }
    
    /**
     * LSP初期化
     */
    public function initialize() {
        $data = json_decode(file_get_contents('php://input'), true);
        $rootPath = $data['rootPath'] ?? null;
        
        $result = $this->server->initialize($rootPath);
        
        echo json_encode([
            'success' => true,
            'result' => $result
        ]);
    }
    
    /**
     * ホバー情報
     */
    public function hover() {
        $data = json_decode(file_get_contents('php://input'), true);
        $filePath = $data['filePath'] ?? null;
        $line = $data['line'] ?? 0;
        $character = $data['character'] ?? 0;
        
        if (!$filePath) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'filePath is required']);
            return;
        }
        
        $result = $this->server->getHover($filePath, $line, $character);
        
        echo json_encode([
            'success' => true,
            'result' => $result
        ]);
    }
    
    /**
     * コンプリーション
     */
    public function completion() {
        $data = json_decode(file_get_contents('php://input'), true);
        $filePath = $data['filePath'] ?? null;
        $line = $data['line'] ?? 0;
        $character = $data['character'] ?? 0;
        
        if (!$filePath) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'filePath is required']);
            return;
        }
        
        $items = $this->server->getCompletion($filePath, $line, $character);
        
        echo json_encode([
            'success' => true,
            'items' => $items
        ]);
    }
    
    /**
     * 定義への移動
     */
    public function definition() {
        $data = json_decode(file_get_contents('php://input'), true);
        $filePath = $data['filePath'] ?? null;
        $line = $data['line'] ?? 0;
        $character = $data['character'] ?? 0;
        
        if (!$filePath) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'filePath is required']);
            return;
        }
        
        $result = $this->server->getDefinition($filePath, $line, $character);
        
        echo json_encode([
            'success' => true,
            'result' => $result
        ]);
    }
    
    /**
     * ドキュメントシンボル
     */
    public function documentSymbols() {
        $data = json_decode(file_get_contents('php://input'), true);
        $filePath = $data['filePath'] ?? null;
        
        if (!$filePath) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'filePath is required']);
            return;
        }
        
        $symbols = $this->server->getDocumentSymbols($filePath);
        
        echo json_encode([
            'success' => true,
            'symbols' => $symbols
        ]);
    }
}
