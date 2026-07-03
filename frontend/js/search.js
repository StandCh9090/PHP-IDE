/**
 * 検索・置換機能
 */
const SearchManager = {
    searchResults: [],
    currentResultIndex: 0,
    searchPanel: null,

    /**
     * 初期化
     */
    init() {
        // 検索パネルを作成
        const searchPanel = document.createElement('div');
        searchPanel.id = 'searchPanel';
        searchPanel.style.cssText = `
            position: fixed;
            top: 40px;
            right: 0;
            width: 350px;
            height: auto;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
        `;

        searchPanel.innerHTML = `
            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                <input id="searchInput" type="text" placeholder="\ud83d\udd0d \u691c\u7d22..." 
                    style="flex: 1; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                <button id="closeSearch" style="background: none; border: none; cursor: pointer; color: var(--text-primary);">\u2715</button>
            </div>
            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                <input id="replaceInput" type="text" placeholder="\u7f6e\u63db..." 
                    style="flex: 1; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                <button id="replaceAllBtn" style="padding: 8px 12px; background: var(--accent-color); color: white; border: none; border-radius: 4px; cursor: pointer;">
                    \u7f6e\u63db
                </button>
            </div>
            <div id="searchResults" style="max-height: 400px; overflow-y: auto; font-size: 12px;"></div>
        `;

        document.body.appendChild(searchPanel);
        this.searchPanel = searchPanel;

        this.setupEventListeners();
    },

    /**
     * 検索を実行
     */
    async search(query) {
        if (!query) return;

        try {
            const response = await API.post('/search/find', {
                query: query,
                path: '/',
                regex: false
            });

            if (response.success) {
                this.searchResults = response.results;
                this.displayResults(response.results);
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    },

    /**
     * 検\u7d22\u7d50\u679c\u3092\u8868\u793a
     */
    displayResults(results) {
        const resultsDiv = document.getElementById('searchResults');
        resultsDiv.innerHTML = '';

        if (results.length === 0) {
            resultsDiv.innerHTML = '<div style="padding: 8px; color: var(--text-secondary);">\u7d50\u679c\u306a\u3057</div>';
            return;
        }

        results.forEach((result, index) => {
            const fileDiv = document.createElement('div');
            fileDiv.style.cssText = 'padding: 4px; border-bottom: 1px solid var(--border-color);';
            
            const fileName = document.createElement('div');
            fileName.style.cssText = 'font-weight: bold; cursor: pointer; padding: 4px; border-radius: 2px;';
            fileName.textContent = result.file + ` (${result.matches.length})`;
            fileName.addEventListener('click', () => {
                EditorManager.openFile(result.file);
            });

            fileDiv.appendChild(fileName);

            result.matches.forEach(match => {
                const matchDiv = document.createElement('div');
                matchDiv.style.cssText = 'padding: 4px 8px; cursor: pointer; border-radius: 2px;';
                matchDiv.textContent = `Line ${match.line}: ${match.text}`;
                matchDiv.addEventListener('mouseover', () => {
                    matchDiv.style.background = 'var(--bg-tertiary)';
                });
                matchDiv.addEventListener('mouseout', () => {
                    matchDiv.style.background = '';
                });
                matchDiv.addEventListener('click', () => {
                    EditorManager.openFile(result.file);
                });
                fileDiv.appendChild(matchDiv);
            });

            resultsDiv.appendChild(fileDiv);
        });
    },

    /**
     * \u7f6e\u63db\u3092\u5b9f\u884c
     */
    async replace(query, replacement) {
        if (!query) return;

        try {
            const response = await API.post('/search/replace', {
                query: query,
                replacement: replacement,
                path: '/',
                regex: false,
                replaceAll: true
            });

            if (response.success) {
                UI.showNotification(`${response.count} \u3001\u7f6e\u63db\u3057\u307e\u3057\u305f`, 'success');
                FileTreeManager.loadTree();
            }
        } catch (error) {
            console.error('Replace error:', error);
            UI.showNotification('\u7f6e\u63db\u5931\u6557: ' + error.message, 'error');
        }
    },

    /**
     * \u30a4\u30d9\u30f3\u30c8\u30ea\u30b9\u30ca\u30fc
     */
    setupEventListeners() {
        // Ctrl+H \u3092\u6f15\u304d\u51fa\u3059
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'h') {
                e.preventDefault();
                this.searchPanel.style.display = this.searchPanel.style.display === 'none' ? 'block' : 'none';
                if (this.searchPanel.style.display === 'block') {
                    document.getElementById('searchInput').focus();
                }
            }
        });

        document.getElementById('closeSearch').addEventListener('click', () => {
            this.searchPanel.style.display = 'none';
        });

        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.search(e.target.value);
        });

        document.getElementById('replaceAllBtn').addEventListener('click', () => {
            const searchInput = document.getElementById('searchInput');
            const replaceInput = document.getElementById('replaceInput');
            this.replace(searchInput.value, replaceInput.value);
        });
    }
};
