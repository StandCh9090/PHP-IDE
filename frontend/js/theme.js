/**
 * テーマ管理
 */
const ThemeManager = {
    currentTheme: localStorage.getItem('theme') || 'light',

    /**
     * 初期化
     */
    init() {
        this.setTheme(this.currentTheme);
        this.setupEventListeners();
    },

    /**
     * テーマ設定
     */
    setTheme(theme) {
        const isDark = theme === 'dark';
        
        if (isDark) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
        
        this.currentTheme = theme;
        localStorage.setItem('theme', theme);
        
        if (EditorManager.editor) {
            EditorManager.setTheme(theme);
        }
    },

    /**
     * テーマ切り替え
     */
    toggle() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.setTheme(newTheme);
    },

    /**
     * イベントリスナーセットアップ
     */
    setupEventListeners() {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggle();
                themeToggle.textContent = this.currentTheme === 'dark' ? '☀️ テーマ' : '🌙 テーマ';
            });
        }
    }
};