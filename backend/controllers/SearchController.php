<?php
/**
 * 検索コントローラー
 */
class SearchController {
    /**
     * テキスト検索
     */
    public function search() {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = $data['query'] ?? '';
        $path = $data['path'] ?? '/';
        $regex = $data['regex'] ?? false;
        
        if (empty($query)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Query is required']);
            return;
        }
        
        $fullPath = $this->getFullPath($path);
        $results = $this->searchFiles($fullPath, $query, $regex);
        
        echo json_encode([
            'success' => true,
            'query' => $query,
            'results' => $results,
            'count' => count($results)
        ]);
    }
    
    /**
     * 置換
     */
    public function replace() {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = $data['query'] ?? '';
        $replacement = $data['replacement'] ?? '';
        $path = $data['path'] ?? '/';
        $regex = $data['regex'] ?? false;
        $replaceAll = $data['replaceAll'] ?? false;
        
        if (empty($query)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Query is required']);
            return;
        }
        
        $fullPath = $this->getFullPath($path);
        $count = $this->replaceInFiles($fullPath, $query, $replacement, $regex, $replaceAll);
        
        echo json_encode([
            'success' => true,
            'message' => "Replaced in {$count} files",
            'count' => $count
        ]);
    }
    
    /**
     * フィル内検索
     */
    private function searchFiles($dir, $query, $regex = false) {
        $results = [];
        
        if (!is_dir($dir)) {
            return $results;
        }
        
        try {
            $iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $recursive = new RecursiveIteratorIterator($iterator);
            
            foreach ($recursive as $file) {
                if (!is_file($file)) continue;
                
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (!in_array($ext, ALLOWED_EXTENSIONS)) continue;
                
                $content = @file_get_contents($file);
                if ($content === false) continue;
                
                if (filesize($file) > MAX_FILE_SIZE) continue;
                
                $matches = $this->findMatches($content, $query, $regex);
                
                if (!empty($matches)) {
                    $relativePath = str_replace(WORKSPACE_DIR, '', $file);
                    $results[] = [
                        'file' => $relativePath,
                        'matches' => $matches
                    ];
                }
            }
        } catch (Exception $e) {
            // 読み込みエラーを無視
        }
        
        return $results;
    }
    
    /**
     * マッチを探す
     */
    private function findMatches($content, $query, $regex = false) {
        $matches = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $lineNum => $line) {
            $lineMatches = [];
            
            if ($regex) {
                if (@preg_match('/' . str_replace('/', '\\/', $query) . '/', $line, $m, PREG_OFFSET_CAPTURE)) {
                    foreach ($m as $match) {
                        $lineMatches[] = [
                            'text' => $match[0],
                            'offset' => $match[1]
                        ];
                    }
                }
            } else {
                $pos = 0;
                while (($pos = strpos($line, $query, $pos)) !== false) {
                    $lineMatches[] = [
                        'text' => $query,
                        'offset' => $pos
                    ];
                    $pos += strlen($query);
                }
            }
            
            if (!empty($lineMatches)) {
                $matches[] = [
                    'line' => $lineNum + 1,
                    'text' => trim($line),
                    'matches' => $lineMatches
                ];
            }
        }
        
        return $matches;
    }
    
    /**
     * ファイル内を置換
     */
    private function replaceInFiles($dir, $query, $replacement, $regex = false, $replaceAll = true) {
        $count = 0;
        
        try {
            $iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $recursive = new RecursiveIteratorIterator($iterator);
            
            foreach ($recursive as $file) {
                if (!is_file($file)) continue;
                
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (!in_array($ext, ALLOWED_EXTENSIONS)) continue;
                
                $content = @file_get_contents($file);
                if ($content === false) continue;
                
                $newContent = $content;
                
                if ($regex) {
                    $newContent = @preg_replace('/' . str_replace('/', '\\/', $query) . '/', $replacement, $content);
                } else {
                    $newContent = str_replace($query, $replacement, $content);
                }
                
                if ($newContent !== $content) {
                    if (@file_put_contents($file, $newContent) !== false) {
                        $count++;
                    }
                }
            }
        } catch (Exception $e) {
            // 読み込みエラーを無視
        }
        
        return $count;
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
