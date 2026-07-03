<?php
/**
 * Git統統コントローラー
 */
class GitController {
    private $workspaceDir;
    
    public function __construct() {
        $this->workspaceDir = WORKSPACE_DIR;
    }
    
    /**
     * リポジトリ初期化
     */
    public function init() {
        $result = $this->executeGit('init');
        
        echo json_encode([
            'success' => !$result['error'],
            'message' => $result['output'] ?? 'Repository initialized',
            'error' => $result['error'] ?? null
        ]);
    }
    
    /**
     * ステータスを取得
     */
    public function status() {
        $result = $this->executeGit('status --porcelain');
        
        $files = [];
        if (!$result['error'] && !empty($result['output'])) {
            foreach (explode("\n", $result['output']) as $line) {
                if (!empty(trim($line))) {
                    $status = substr($line, 0, 2);
                    $file = substr($line, 3);
                    $files[] = ['status' => $status, 'file' => $file];
                }
            }
        }
        
        echo json_encode([
            'success' => !$result['error'],
            'files' => $files,
            'error' => $result['error'] ?? null
        ]);
    }
    
    /**
     * コミット
     */
    public function commit() {
        $data = json_decode(file_get_contents('php://input'), true);
        $message = $data['message'] ?? '';
        
        if (empty($message)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Commit message is required']);
            return;
        }
        
        // 全てをステージング
        $this->executeGit('add -A');
        
        // コミット
        $result = $this->executeGit('commit -m ' . escapeshellarg($message));
        
        echo json_encode([
            'success' => !$result['error'],
            'message' => $result['output'] ?? 'Committed successfully',
            'error' => $result['error'] ?? null
        ]);
    }
    
    /**
     * ログを取得
     */
    public function log() {
        $limit = $_GET['limit'] ?? 10;
        $result = $this->executeGit('log --oneline -n ' . (int)$limit);
        
        $commits = [];
        if (!$result['error'] && !empty($result['output'])) {
            foreach (explode("\n", $result['output']) as $line) {
                if (!empty(trim($line))) {
                    $parts = explode(' ', $line, 2);
                    $commits[] = [
                        'hash' => $parts[0] ?? '',
                        'message' => $parts[1] ?? ''
                    ];
                }
            }
        }
        
        echo json_encode([
            'success' => !$result['error'],
            'commits' => $commits,
            'error' => $result['error'] ?? null
        ]);
    }
    
    /**
     * Gitコマンドを実行
     */
    private function executeGit($command) {
        $fullCommand = 'cd ' . escapeshellarg($this->workspaceDir) . ' && git ' . $command . ' 2>&1';
        
        exec($fullCommand, $output, $returnCode);
        
        return [
            'error' => $returnCode !== 0,
            'output' => implode("\n", $output),
            'returnCode' => $returnCode
        ];
    }
}
