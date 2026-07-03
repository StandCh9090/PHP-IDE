<?php
/**
 * ターミナル・コマンド実行コントローラー
 */
class TerminalController {
    /**
     * コマンドを実行
     */
    public function executeCommand() {
        $data = json_decode(file_get_contents('php://input'), true);
        $command = $data['command'] ?? null;
        
        if (!$command) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Command is required']);
            return;
        }
        
        // コマンドをフィルタリング
        if (!$this->isCommandAllowed($command)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Command is not allowed']);
            return;
        }
        
        try {
            // PHPスクリプト実行
            if (preg_match('/^php\s+(.+)$/', $command, $matches)) {
                $filePath = $matches[1];
                $fullPath = $this->getFullPath($filePath);
                
                if (!file_exists($fullPath)) {
                    throw new Exception('File not found');
                }
                
                ob_start();
                $result = include $fullPath;
                $output = ob_get_clean();
                
                echo json_encode([
                    'success' => true,
                    'output' => $output ?: 'Script executed successfully'
                ]);
                return;
            }
            
            // その他のコマンド（ローカルコマンド実行は制限）
            if (in_array($command, ['help', 'clear', 'info'])) {
                $output = $this->executeBuiltinCommand($command);
                echo json_encode([
                    'success' => true,
                    'output' => $output
                ]);
                return;
            }
            
            throw new Exception('Unsupported command');
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * 組み込みコマンドを実行
     */
    private function executeBuiltinCommand($command) {
        switch ($command) {
            case 'help':
                return implode("\n", [
                    'Available commands:',
                    'php <file>    - Execute PHP script',
                    'help           - Show this help message',
                    'clear          - Clear terminal',
                    'info           - Show PHP info'
                ]);
            
            case 'info':
                return 'PHP IDE v1.0 | PHP ' . phpversion();
            
            case 'clear':
                return ''; // フロントで処理
            
            default:
                return 'Unknown command';
        }
    }
    
    /**
     * コマンドが許可されているか確認
     */
    private function isCommandAllowed($command) {
        // 許可されたコマンドパターン
        $allowed = [
            '/^php\s+/',
            '/^help$/',
            '/^clear$/',
            '/^info$/'
        ];
        
        foreach ($allowed as $pattern) {
            if (preg_match($pattern, trim($command))) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 相対パスをフルパスに変換
     */
    private function getFullPath($path) {
        $path = ltrim($path, '/');
        $fullPath = WORKSPACE_DIR . DIRECTORY_SEPARATOR . $path;
        
        $realPath = realpath(dirname($fullPath));
        if (!$realPath || strpos($realPath, WORKSPACE_DIR) !== 0) {
            throw new Exception('Invalid path');
        }
        
        return $fullPath;
    }
}
