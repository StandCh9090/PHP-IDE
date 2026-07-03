<?php
/**
 * ファイル操作コントローラー
 */
class FileController {
    /**
     * ファイルツリーを取得
     */
    public function getFileTree() {
        $path = $_GET['path'] ?? '/';
        $fullPath = $this->getFullPath($path);
        
        if (!is_dir($fullPath)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Path is not a directory'
            ]);
            return;
        }
        
        $files = $this->scanDirectory($fullPath);
        echo json_encode([
            'success' => true,
            'files' => $files
        ]);
    }
    
    /**
     * ファイル内容を取得
     */
    public function getFile() {
        $path = $_GET['path'] ?? null;
        
        if (!$path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Path is required']);
            return;
        }
        
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath) || !is_file($fullPath)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'File not found']);
            return;
        }
        
        if (filesize($fullPath) > MAX_FILE_SIZE) {
            http_response_code(413);
            echo json_encode(['success' => false, 'error' => 'File is too large']);
            return;
        }
        
        $content = file_get_contents($fullPath);
        echo json_encode([
            'success' => true,
            'content' => $content,
            'size' => filesize($fullPath),
            'lastModified' => filemtime($fullPath)
        ]);
    }
    
    /**
     * ファイルを作成
     */
    public function createFile() {
        $data = json_decode(file_get_contents('php://input'), true);
        $path = $data['path'] ?? null;
        $content = $data['content'] ?? '';
        
        if (!$path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Path is required']);
            return;
        }
        
        $fullPath = $this->getFullPath($path);
        $directory = dirname($fullPath);
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        if (file_exists($fullPath)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'File already exists']);
            return;
        }
        
        if (file_put_contents($fullPath, $content) === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to create file']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'File created successfully',
            'path' => $path
        ]);
    }
    
    /**
     * ファイルを保存
     */
    public function saveFile() {
        $data = json_decode(file_get_contents('php://input'), true);
        $path = $data['path'] ?? null;
        $content = $data['content'] ?? '';
        
        if (!$path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Path is required']);
            return;
        }
        
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'File not found']);
            return;
        }
        
        if (file_put_contents($fullPath, $content) === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to save file']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'File saved successfully',
            'lastModified' => time()
        ]);
    }
    
    /**
     * ファイルを削除
     */
    public function deleteFile() {
        $path = $_GET['path'] ?? null;
        
        if (!$path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Path is required']);
            return;
        }
        
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'File not found']);
            return;
        }
        
        if (is_dir($fullPath)) {
            $this->removeDirectory($fullPath);
        } else {
            unlink($fullPath);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'File deleted successfully'
        ]);
    }
    
    /**
     * フォルダを作成
     */
    public function createFolder() {
        $data = json_decode(file_get_contents('php://input'), true);
        $path = $data['path'] ?? null;
        
        if (!$path) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Path is required']);
            return;
        }
        
        $fullPath = $this->getFullPath($path);
        
        if (file_exists($fullPath)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Folder already exists']);
            return;
        }
        
        if (!mkdir($fullPath, 0755, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to create folder']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Folder created successfully',
            'path' => $path
        ]);
    }
    
    /**
     * ファイルを名前変更
     */
    public function renameFile() {
        $data = json_decode(file_get_contents('php://input'), true);
        $oldPath = $data['oldPath'] ?? null;
        $newPath = $data['newPath'] ?? null;
        
        if (!$oldPath || !$newPath) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'oldPath and newPath are required']);
            return;
        }
        
        $fullOldPath = $this->getFullPath($oldPath);
        $fullNewPath = $this->getFullPath($newPath);
        
        if (!file_exists($fullOldPath)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'File not found']);
            return;
        }
        
        if (!rename($fullOldPath, $fullNewPath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to rename file']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'File renamed successfully',
            'newPath' => $newPath
        ]);
    }
    
    /**
     * ディレクトリ走査（再帰的）
     */
    private function scanDirectory($dir, $maxDepth = 2, $currentDepth = 0) {
        $items = [];
        
        if ($currentDepth >= $maxDepth) {
            return $items;
        }
        
        try {
            $entries = scandir($dir);
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') continue;
                if ($entry[0] === '.') continue; // 隠しファイル除外
                
                $fullPath = $dir . DIRECTORY_SEPARATOR . $entry;
                $relativePath = str_replace(WORKSPACE_DIR, '', $fullPath);
                
                $item = [
                    'name' => $entry,
                    'path' => $relativePath,
                    'type' => is_dir($fullPath) ? 'folder' : 'file',
                    'size' => is_file($fullPath) ? filesize($fullPath) : 0,
                    'modified' => filemtime($fullPath)
                ];
                
                if (is_dir($fullPath)) {
                    $item['children'] = $this->scanDirectory($fullPath, $maxDepth, $currentDepth + 1);
                }
                
                $items[] = $item;
            }
            usort($items, function($a, $b) {
                if ($a['type'] !== $b['type']) {
                    return $a['type'] === 'folder' ? -1 : 1;
                }
                return strcasecmp($a['name'], $b['name']);
            });
        } catch (Exception $e) {
            // ディレクトリ読み込み失敗
        }
        
        return $items;
    }
    
    /**
     * 相対パスをフルパスに変換
     */
    private function getFullPath($path) {
        $path = ltrim($path, '/');
        $fullPath = WORKSPACE_DIR . DIRECTORY_SEPARATOR . $path;
        
        // パストラバーサル攻撃対策
        $realPath = realpath(dirname($fullPath));
        if (!$realPath || strpos($realPath, WORKSPACE_DIR) !== 0) {
            throw new Exception('Invalid path');
        }
        
        return $fullPath;
    }
    
    /**
     * ディレクトリを再帰的に削除
     */
    private function removeDirectory($dir) {
        if (is_dir($dir)) {
            $entries = scandir($dir);
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') continue;
                $fullPath = $dir . DIRECTORY_SEPARATOR . $entry;
                if (is_dir($fullPath)) {
                    $this->removeDirectory($fullPath);
                } else {
                    unlink($fullPath);
                }
            }
            rmdir($dir);
        }
    }
}
