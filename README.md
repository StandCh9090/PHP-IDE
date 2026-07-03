# PHP-IDE

VSCodeライクなウェブベースのコードエディター。PHPバックエンド＋Monaco Editorで実装。

## 機能

- ✅ **コード表示・編集** - Monaco Editorによる高速な編集体験
- ✅ **シンタックスハイライト** - 複数言語対応
- ✅ **ファイルツリー** - フォルダ構造の表示・管理
- ✅ **検索・置換** - テキスト検索・正規表現対応
- ✅ **テーマシステム** - ダーク/ライトモード切り替え
- ✅ **言語サーバープロトコル(LSP)** - インテリセンス、型チェック
- ✅ **ターミナル統合** - コマンド実行

## 技術スタック

### バックエンド
- **言語**: PHP 7.4+
- **API**: REST API + WebSocket
- **解析**: PHP Parser（構文解析）

### フロントエンド
- **エディター**: Monaco Editor（Microsoft）
- **UI**: Vanilla JavaScript + CSS
- **WebSocket**: リアルタイム更新

## セットアップ

```bash
# クローン
git clone https://github.com/StandCh9090/PHP-IDE.git
cd PHP-IDE

# PHPサーバー起動
php -S localhost:8000 -t . backend/index.php

# ブラウザで開く
http://localhost:8000
```

## ディレクトリ構造

```
PHP-IDE/
├── backend/              # PHPバックエンド
├── frontend/             # フロントエンド（HTML/CSS/JS）
└── README.md
```

## ライセンス

MIT
