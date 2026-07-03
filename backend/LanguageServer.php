<?php
/**
 * 言語サーバープロトコル (LSP) 基本実装
 */
class LanguageServer {
    private $capabilities = [];
    private $workspaceRoot = null;
    private $isInitialized = false;
    
    public function __construct() {
        $this->capabilities = [
            'hoverProvider' => true,
            'completionProvider' => [
                'resolveProvider' => true,
                'triggerCharacters' => ['$', '>', '-', '\\', '(']
            ],
            'definitionProvider' => true,
            'referencesProvider' => true,
            'documentSymbolProvider' => true,
            'workspaceSymbolProvider' => true,
            'codeActionProvider' => true,
            'documentFormattingProvider' => true,
            'documentRangeFormattingProvider' => true
        ];
    }
    
    /**
     * 初期化
     */
    public function initialize($rootPath = null) {
        $this->workspaceRoot = $rootPath ?: WORKSPACE_DIR;
        $this->isInitialized = true;
        
        return [
            'capabilities' => $this->capabilities,
            'serverInfo' => [
                'name' => 'PHP-IDE Language Server',
                'version' => '1.0'
            ]
        ];
    }
    
    /**
     * ホバー情報を取得
     */
    public function getHover($filePath, $line, $character) {
        if (!$this->isInitialized) {
            return null;
        }
        
        $fullPath = $this->getFullPath($filePath);
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $content = file_get_contents($fullPath);
        $lines = explode("\n", $content);
        
        if (!isset($lines[$line - 1])) {
            return null;
        }
        
        $currentLine = $lines[$line - 1];
        $word = $this->getWordAtPosition($currentLine, $character);
        
        if (empty($word)) {
            return null;
        }
        
        return [
            'contents' => 'Symbol: ' . $word
        ];
    }
    
    /**
     * コンプリーションを取得
     */
    public function getCompletion($filePath, $line, $character) {
        if (!$this->isInitialized) {
            return [];
        }
        
        $fullPath = $this->getFullPath($filePath);
        if (!file_exists($fullPath)) {
            return [];
        }
        
        $content = file_get_contents($fullPath);
        $lines = explode("\n", $content);
        
        if (!isset($lines[$line - 1])) {
            return [];
        }
        
        $currentLine = $lines[$line - 1];
        $word = $this->getWordAtPosition($currentLine, $character);
        
        // PHP組み込み関数の例
        $suggestions = [
            ['label' => 'echo', 'kind' => 4],
            ['label' => 'print', 'kind' => 4],
            ['label' => 'isset', 'kind' => 4],
            ['label' => 'empty', 'kind' => 4],
            ['label' => 'array', 'kind' => 4],
            ['label' => 'explode', 'kind' => 4],
            ['label' => 'implode', 'kind' => 4],
            ['label' => 'trim', 'kind' => 4],
            ['label' => 'strlen', 'kind' => 4],
            ['label' => 'count', 'kind' => 4],
            ['label' => 'file_get_contents', 'kind' => 4],
            ['label' => 'file_put_contents', 'kind' => 4]
        ];
        
        // フィルタリング
        if (!empty($word)) {
            $suggestions = array_filter($suggestions, function($item) use ($word) {
                return strpos($item['label'], $word) === 0;
            });
        }
        
        return array_values($suggestions);
    }
    
    /**
     * 定義を探す
     */
    public function getDefinition($filePath, $line, $character) {
        // 簡単な実装の例
        return [
            'uri' => $filePath,
            'range' => [
                'start' => ['line' => $line - 1, 'character' => 0],
                'end' => ['line' => $line - 1, 'character' => 50]
            ]
        ];
    }
    
    /**
     * シンボル情報を取得
     */
    public function getDocumentSymbols($filePath) {
        $fullPath = $this->getFullPath($filePath);
        
        if (!file_exists($fullPath)) {
            return [];
        }
        
        $content = file_get_contents($fullPath);
        $symbols = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $lineNum => $line) {
            // 関数定義を検索
            if (preg_match('/function\s+(\w+)\s*\(/', $line, $matches)) {
                $symbols[] = [
                    'name' => $matches[1],
                    'kind' => 12, // Function
                    'location' => [
                        'uri' => $filePath,
                        'range' => [
                            'start' => ['line' => $lineNum, 'character' => 0],
                            'end' => ['line' => $lineNum, 'character' => strlen($line)]
                        ]
                    ]
                ];
            }
            
            // クラス定義を検索
            if (preg_match('/class\s+(\w+)/', $line, $matches)) {
                $symbols[] = [
                    'name' => $matches[1],
                    'kind' => 5, // Class
                    'location' => [
                        'uri' => $filePath,
                        'range' => [
                            'start' => ['line' => $lineNum, 'character' => 0],
                            'end' => ['line' => $lineNum, 'character' => strlen($line)]
                        ]
                    ]
                ];
            }
        }
        
        return $symbols;
    }
    
    /**
     * 指定位置をある午値を取得
     */
    private function getWordAtPosition($line, $character) {
        $start = $character;
        $end = $character;
        
        // 先头からスキャン
        while ($start > 0 && preg_match('/[\w]/', $line[$start - 1])) {
            $start--;
        }
        
        // 末尾までスキャン
        while ($end < strlen($line) && preg_match('/[\w]/', $line[$end])) {
            $end++;
        }
        
        return substr($line, $start, $end - $start);
    }
    
    /**
     * 相対パスをフルパスに変換
     */
    private function getFullPath($path) {
        $path = ltrim($path, '/');
        return $this->workspaceRoot . DIRECTORY_SEPARATOR . $path;
    }
}
