<?php
/**
 * グローバル設定
 */

// ディレクトリ設定
define('BASE_DIR', __DIR__);
define('WORKSPACE_DIR', dirname(__DIR__) . '/workspace');
define('BACKEND_DIR', __DIR__);

// セキュリティ設定
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['php', 'js', 'ts', 'html', 'css', 'json', 'md', 'sql', 'py', 'java', 'xml', 'yaml']);
define('ALLOWED_FILE_OPERATIONS', ['read', 'write', 'create', 'delete']);

// ワークスペースディレクトリが存在しない場合は作成
if (!file_exists(WORKSPACE_DIR)) {
    mkdir(WORKSPACE_DIR, 0755, true);
}

// エラーハンドリング
error_reporting(E_ALL);
ini_set('display_errors', false);
ini_set('log_errors', true);

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');
