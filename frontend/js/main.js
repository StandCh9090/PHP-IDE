/**
 * メイン初期化
 */
async function initializeApp() {
    try {
        console.log('Initializing PHP-IDE...');
        
        // テーマ初期化
        ThemeManager.init();
        
        // エディター初期化
        await EditorManager.init();
        
        // UI初期化
        TabManager.init();
        TerminalManager.init();
        
        // ファイルツリー初期化
        await FileTreeManager.init();
        
        // イベントリスナー
        setupEventListeners();
        
        console.log('PHP-IDE initialized successfully!');
        UI.showNotification('PHP-IDEが起動しました', 'success');
    } catch (error) {
        console.error('Initialization error:', error);
        UI.showNotification(`初期化エラー: ${error.message}`, 'error');
    }
}

/**
 * イベントリスナーセットアップ
 */
function setupEventListeners() {
    // ファイルメニュー
    document.getElementById('newFileBtn').addEventListener('click', async () => {
        const fileName = prompt('ファイル名を入力:');
        if (fileName) {
            try {
                const response = await API.createFile(`/${fileName}`, '');
                if (response.success) {
                    await FileTreeManager.loadTree();
                    UI.showNotification(`作成しました: ${fileName}`, 'success');
                }
            } catch (error) {
                UI.showNotification(`作成に失敗しました: ${error.message}`, 'error');
            }
        }
    });

    // フォルダメニュー
    document.getElementById('newFolderBtn').addEventListener('click', async () => {
        const folderName = prompt('フォルダ名を入力:');
        if (folderName) {
            try {
                const response = await API.createFolder(`/${folderName}`);
                if (response.success) {
                    await FileTreeManager.loadTree();
                    UI.showNotification(`作成しました: ${folderName}`, 'success');
                }
            } catch (error) {
                UI.showNotification(`作成に失敗しました: ${error.message}`, 'error');
            }
        }
    });

    // サイドバー切り替え
    document.getElementById('toggleSidebar').addEventListener('click', () => {
        UI.toggleSidebar();
    });

    // パネル切り替え
    document.querySelectorAll('.panel-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const panelName = tab.dataset.panel;
            UI.switchPanel(panelName);
        });
    });

    // メニューボタン
    document.getElementById('fileMenu').addEventListener('click', () => {
        UI.showNotification('ファイルメニューをクリック', 'info');
    });

    document.getElementById('editMenu').addEventListener('click', () => {
        UI.showNotification('編集メニューをクリック', 'info');
    });

    document.getElementById('viewMenu').addEventListener('click', () => {
        UI.showNotification('表示メニューをクリック', 'info');
    });

    document.getElementById('toolsMenu').addEventListener('click', () => {
        UI.showNotification('ツールメニューをクリック', 'info');
    });
}

// DOM ロード完了時に初期化
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}
