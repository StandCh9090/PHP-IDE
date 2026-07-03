# PHP-IDE セットアップガイド

## 前提条件

- **PHP** 7.4 以上
- **Apache** または **nginx**
- **Composer** (オプション)

## インストール

### 1. リポジトリクローン

```bash
git clone https://github.com/StandCh9090/PHP-IDE.git
cd PHP-IDE
```

### 2. ワークスペースディレクトリ作成

```bash
mkdir workspace
chmod 755 workspace
```

### 3. Apache/nginx 設定

#### Apache 設定

`.htaccess` ファイルを根ディレクトリに配置しています。

```apache
<VirtualHost *:80>
    ServerName php-ide.local
    DocumentRoot /path/to/PHP-IDE
    
    <Directory /path/to/PHP-IDE>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### nginx 設定

```nginx
server {
    listen 80;
    server_name php-ide.local;
    root /path/to/PHP-IDE;
    index index.html;
    
    location /api/ {
        try_files $uri $uri/ /backend/index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. ローカル開発用 (推奨)

```bash
# PHP組み込みサーバーを起動
cd /path/to/PHP-IDE
php -S localhost:8000 -t . backend/index.php
```

ブラウザで `http://localhost:8000` を開けば完了です。

## ディレクトリ構造

```
PHP-IDE/
├── index.html                   # メインページ
├── .htaccess                  # Apache設定
├── frontend/
├─  ├── css/
├─  │  └── style.css          # VSCode適編スタイル
├─  └── js/
├─      ├── config.js        # グローバル設定
├─      ├── api.js           # API通信
├─      ├── editor.js        # Monaco Editor管理
├─      ├── fileTree.js      # ファイルツリー
├─      ├── tabs.js          # タブバー
├─      ├── terminal.js      # ターミナル
├─      ├── theme.js         # テーマ管理
├─      ├── ui.js            # UIユーティリティ
├─      └── main.js          # 初期化スクリプト
├── backend/
├─  ├── index.php               # REST APIエントリー
├─  ├── config.php              # グローバル設定
├─  ├── Router.php              # ルーター
├─  ├── LanguageServer.php      # LSP実装
├─  └── controllers/
├─      ├── FileController.php
├─      ├── TerminalController.php
├─      └── AnalyzerController.php
├── workspace/                    # ユーザーファイル置き場
├── README.md
└── SETUP.md                   # このファイル
```

## APIエンドポイント

### ファイル操作

| メソッド | エンドポイント | 説明 |
|--------|----------------|--------|
| GET | `/api/files` | ファイルツリー取得 |
| GET | `/api/file` | ファイル内容取得 |
| POST | `/api/file/create` | ファイル作成 |
| POST | `/api/file/save` | ファイル保存 |
| DELETE | `/api/file` | ファイル削除 |
| POST | `/api/folder/create` | フォルダ作成 |
| PUT | `/api/file/rename` | ファイル名変更 |

### ターミナル

| メソッド | エンドポイント | 説明 |
|--------|----------------------------|--------|
| POST | `/api/terminal/execute` | コマンド実行 |

### コード解析

| メソッド | エンドポイント | 説明 |
|--------|---------------------------|--------|
| POST | `/api/analyze/syntax` | 構文解析 |
| POST | `/api/analyze/lint` | コード品質チェック |
| POST | `/api/analyze/format` | コードフォーマット |

## 使用方法

### ファイルを開く

1. 左サイドバーのファイルをクリック
2. エディター内容が表示されて Monaco Editor で編集可能

### コードを実行

1. ターミナルパネルをクリック
2. `php filename.php` を入力して実行

### テーマを変更

1. 上部メニューの 🌙 テーマをクリック
2. ダーク/ライトを切り替え

## 技術仕様

### フロントエンド
- **Editor**: Monaco Editor (VSCode同一エンジン)
- **UI**: Vanilla JavaScript + CSS
- **逻理**: JavaScript Modules

### バックエンド
- **言語**: PHP 7.4+
- **アーキテクチャ**: MVCパターン
- **API**: REST
- **セキュリティ**: パストラバーサル攻撃対策

## 特征

- ✅ Monaco Editor 統約
- ✅ 複数言語対応
- ✅ ダークテーマ
- ✅ 組み込みターミナル
- ✅ 構文チェック
- ✅ コードフォーマット

## 今後の計画

- 🔧 拡張機能
  - デバッガー統統
  - Git統統
  - DB管理
- 🐛 成能改善
  - 光追的構文解析
  - 初級コンプリート

## ライセンス

MIT
