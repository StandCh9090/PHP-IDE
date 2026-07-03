<?php
/**
 * コード解析コントローラー
 */
class AnalyzerController {
    /**
     * 構文解析
     */
    public function analyzeSyntax() {
        $data = json_decode(file_get_contents('php://input'), true);
        $code = $data['code'] ?? '';
        $language = $data['language'] ?? 'php';
        
        if ($language === 'php') {
            $errors = $this->analyzePHP($code);
        } else {
            $errors = [];
        }
        
        echo json_encode([
            'success' => true,
            'errors' => $errors
        ]);
    }
    
    /**
     * Lint チェック
     */
    public function lint() {
        $data = json_decode(file_get_contents('php://input'), true);
        $code = $data['code'] ?? '';
        $language = $data['language'] ?? 'php';
        
        if ($language === 'php') {
            $issues = $this->lintPHP($code);
        } else {
            $issues = [];
        }
        
        echo json_encode([
            'success' => true,
            'issues' => $issues
        ]);
    }
    
    /**
     * コードをフォーマット
     */
    public function formatCode() {
        $data = json_decode(file_get_contents('php://input'), true);
        $code = $data['code'] ?? '';
        $language = $data['language'] ?? 'php';
        
        if ($language === 'php') {
            $formatted = $this->formatPHP($code);
        } else {
            $formatted = $code;
        }
        
        echo json_encode([
            'success' => true,
            'formatted' => $formatted
        ]);
    }
    
    /**
     * PHP コードの構文解析
     */
    private function analyzePHP($code) {
        $errors = [];
        
        // php -l でチェック
        $tmpFile = tempnam(sys_get_temp_dir(), 'php_');
        file_put_contents($tmpFile, $code);
        
        exec('php -l ' . escapeshellarg($tmpFile), $output, $returnCode);
        
        if ($returnCode !== 0) {
            foreach ($output as $line) {
                if (preg_match('/Parse error.*line (\d+)/', $line, $matches)) {
                    $errors[] = [
                        'line' => (int)$matches[1],
                        'message' => $line,
                        'severity' => 'error'
                    ];
                }
            }
        }
        
        unlink($tmpFile);
        return $errors;
    }
    
    /**
     * PHP コードの Lint チェック
     */
    private function lintPHP($code) {
        $issues = [];
        
        // 簡単な静的解析
        $lines = explode("\n", $code);
        
        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            $lineNum = $lineNum + 1;
            
            // var_dump の使用
            if (preg_match('/var_dump\s*\(/', $line)) {
                $issues[] = [
                    'line' => $lineNum,
                    'message' => 'var_dump usage detected',
                    'severity' => 'warning'
                ];
            }
            
            // print_r の使用
            if (preg_match('/print_r\s*\(/', $line)) {
                $issues[] = [
                    'line' => $lineNum,
                    'message' => 'print_r usage detected',
                    'severity' => 'info'
                ];
            }
            
            // グローバル変数の使用
            if (preg_match('/\$GLOBALS/', $line)) {
                $issues[] = [
                    'line' => $lineNum,
                    'message' => 'GLOBALS usage detected',
                    'severity' => 'warning'
                ];
            }
        }
        
        return $issues;
    }
    
    /**
     * PHP コードをフォーマット
     */
    private function formatPHP($code) {
        // 基本的なフォーマッティング
        $formatted = $code;
        
        // インデントを正規化
        $lines = explode("\n", $formatted);
        $indent = 0;
        $result = [];
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                $result[] = '';
                continue;
            }
            
            // 閉じ括弧でインデント減少
            if (in_array($trimmed[0], ['}', ']', ')'])) {
                $indent = max(0, $indent - 1);
            }
            
            $result[] = str_repeat('    ', $indent) . $trimmed;
            
            // 開き括弧でインデント増加
            if (in_array(substr($trimmed, -1), ['{', '[', '('])) {
                $indent++;
            }
        }
        
        return implode("\n", $result);
    }
}
