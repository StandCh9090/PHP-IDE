<?php
/**
 * デバッガーコントローラー
 */
class DebugController {
    /**
     * ブレークポイントを設定
     */
    public function setBreakpoint() {
        $data = json_decode(file_get_contents('php://input'), true);
        $file = $data['file'] ?? '';
        $line = $data['line'] ?? 0;
        
        if (empty($file) || $line <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid file or line']);
            return;
        }
        
        // ブレークポイント情報をセッションに保存
        if (!isset($_SESSION['breakpoints'])) {
            $_SESSION['breakpoints'] = [];
        }
        
        $breakpointId = md5($file . ':' . $line);
        $_SESSION['breakpoints'][$breakpointId] = [
            'file' => $file,
            'line' => $line,
            'condition' => $data['condition'] ?? null
        ];
        
        echo json_encode([
            'success' => true,
            'breakpointId' => $breakpointId,
            'message' => 'Breakpoint set'
        ]);
    }
    
    /**
     * ブレークポイントを削除
     */
    public function removeBreakpoint() {
        $data = json_decode(file_get_contents('php://input'), true);
        $breakpointId = $data['breakpointId'] ?? '';
        
        if (empty($breakpointId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Breakpoint ID is required']);
            return;
        }
        
        if (isset($_SESSION['breakpoints'][$breakpointId])) {
            unset($_SESSION['breakpoints'][$breakpointId]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Breakpoint removed'
        ]);
    }
    
    /**
     * それっu308c的に実行
     */
    public function step() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // 詳简一的な実装
        echo json_encode([
            'success' => true,
            'message' => 'Step executed',
            'variables' => []
        ]);
    }
    
    /**
     * 実行再開
     */
    public function resume() {
        echo json_encode([
            'success' => true,
            'message' => 'Execution resumed'
        ]);
    }
    
    /**
     * 実行を一殇止め
     */
    public function pause() {
        echo json_encode([
            'success' => true,
            'message' => 'Execution paused'
        ]);
    }
    
    /**
     * 変数情報を取得
     */
    public function getVariables() {
        // 詳简一的な実装
        echo json_encode([
            'success' => true,
            'variables' => [
                [
                    'name' => '$variable',
                    'type' => 'string',
                    'value' => 'example'
                ]
            ]
        ]);
    }
}
