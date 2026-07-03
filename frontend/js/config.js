/**
 * グローバル設定
 */
const CONFIG = {
    // API設定
    API_BASE_URL: '/api',
    API_TIMEOUT: 5000,

    // エディター設定
    EDITOR: {
        fontSize: 14,
        fontFamily: "'Consolas', 'Monaco', 'Courier New', monospace",
        tabSize: 4,
        insertSpaces: true,
        wordWrap: 'on',
        minimap: {
            enabled: true
        },
        theme: 'vs'
    },

    // ファイル設定
    FILE: {
        maxFileSize: 10 * 1024 * 1024,
        autoSaveDelay: 2000,
        supportedLanguages: [
            'php', 'javascript', 'typescript', 'html', 'css',
            'json', 'yaml', 'xml', 'sql', 'python', 'java'
        ]
    },

    // UI設定
    UI: {
        sidebarWidth: 280,
        rightSidebarWidth: 300,
        tabHeight: 35
    },

    // テーマ設定
    THEME: {
        default: localStorage.getItem('theme') || 'light',
        options: ['light', 'dark']
    }
};