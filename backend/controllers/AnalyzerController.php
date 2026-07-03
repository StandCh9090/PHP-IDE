<?php
/**
 * コード解析コントローラー（拡統版）
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
        file_put_contents($tmpFile, '<?php ' . $code);
        
        exec('php -l ' . escapeshellarg($tmpFile) . ' 2>&1', $output, $returnCode);
        
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
        $lines = explode("\n", $code);
        
        foreach ($lines as $lineNum => $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;
            
            $lineNum = $lineNum + 1;
            
            // var_dump の使用
            if (preg_match('/var_dump\s*\(/', $line)) {
                $issues[] = [
                    'line' => $lineNum,
                    'message' => 'var_dumpは開発時のみ使用',
                    'severity' => 'warning'
                ];
            }
            
            // print_r の使用
            if (preg_match('/print_r\s*\(/', $line)) {
                $issues[] = [
                    'line' => $lineNum,
                    'message' => 'print_rが検出',
                    'severity' => 'info'
                ];
            }
            
            // グローバル変数
            if (preg_match('/\$GLOBALS/', $line)) {
                $issues[] = [
                    'line' => $lineNum,
                    'message' => 'グローバル変数の使用を会避',
                    'severity' => 'warning'
                ];
            }
            
            // セミコロンを確認
            if ($trimmed && !preg_match('/[;{}]$/', $trimmed) && !preg_match('/\*\//', $trimmed)) {
                // 次行があり、次行が空でない場合
                if (isset($lines[$lineNum]) && !empty(trim($lines[$lineNum]))) {
                    if (!preg_match('/\(|{|\[|,|=>|\?/', $trimmed)) {
                        // それ以上複雑なチェックは省略
                    }
                }
            }
        }
        
        return $issues;
    }
    
    /**
     * PHP コードをフォーマット
     */
    private function formatPHP($code) {
        $lines = explode("\n", $code);
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
