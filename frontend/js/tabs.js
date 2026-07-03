/**
 * タブ管理
 */
const TabManager = {
    tabs: new Map(),
    tabBar: null,
    activeTab: null,

    /**
     * 初期化
     */
    init() {
        this.tabBar = document.getElementById('tabBar');
        this.setupEventListeners();
    },

    /**
     * タブ追加
     */
    addTab(filePath, fileName) {
        if (this.tabs.has(filePath)) {
            this.activateTab(filePath);
            return;
        }

        const tab = document.createElement('div');
        tab.className = 'tab active';
        tab.dataset.path = filePath;

        const label = document.createElement('span');
        label.textContent = fileName;

        const closeBtn = document.createElement('button');
        closeBtn.className = 'tab-close';
        closeBtn.innerHTML = '✕';
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.closeTab(filePath);
        });

        tab.appendChild(label);
        tab.appendChild(closeBtn);

        tab.addEventListener('click', () => this.activateTab(filePath));

        this.tabBar.appendChild(tab);
        this.tabs.set(filePath, { element: tab, fileName });
        this.activateTab(filePath);
    },

    /**
     * タブ有効化
     */
    activateTab(filePath) {
        if (this.activeTab) {
            this.activeTab.element.classList.remove('active');
        }

        const tab = this.tabs.get(filePath);
        if (tab) {
            tab.element.classList.add('active');
            this.activeTab = tab;
            EditorManager.currentFile = filePath;
            EditorManager.openFile(filePath);
        }
    },

    /**
     * タブ閉じる
     */
    closeTab(filePath) {
        const tab = this.tabs.get(filePath);
        if (tab) {
            tab.element.remove();
            this.tabs.delete(filePath);

            if (this.activeTab && this.activeTab.element === tab.element) {
                const remaining = Array.from(this.tabs.values());
                if (remaining.length > 0) {
                    this.activateTab(Array.from(this.tabs.keys())[0]);
                } else {
                    this.activeTab = null;
                    EditorManager.currentFile = null;
                }
            }
        }
    },

    /**
     * タブステータス更新
     */
    updateTabStatus(filePath) {
        const tab = this.tabs.get(filePath);
        if (tab) {
            const isUnsaved = EditorManager.unsavedFiles.has(filePath);
            const label = tab.element.querySelector('span');
            if (isUnsaved) {
                if (!label.textContent.endsWith('●')) {
                    label.textContent += ' ●';
                }
            } else {
                label.textContent = tab.fileName;
            }
        }
    },

    /**
     * すべてのタブを閉じる
     */
    closeAllTabs() {
        const paths = Array.from(this.tabs.keys());
        paths.forEach(path => this.closeTab(path));
    },

    /**
     * イベントリスナーセットアップ
     */
    setupEventListeners() {
        document.addEventListener('keydown', (e) => {
            // Ctrl+W: タブを閉じる
            if (e.ctrlKey && e.key === 'w') {
                e.preventDefault();
                if (this.activeTab) {
                    this.closeTab(this.activeTab.element.dataset.path);
                }
            }
            // Ctrl+S: 保存
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                EditorManager.saveFile();
            }
        });
    }
};