/**
 * ファイルツリー管理
 */
const FileTreeManager = {
    fileTree: null,
    currentPath: '/',
    selectedNode: null,

    /**
     * 初期化
     */
    async init() {
        this.fileTree = document.getElementById('fileTree');
        await this.loadTree();
        this.setupEventListeners();
    },

    /**
     * ツリーを読み込む
     */
    async loadTree(path = '/') {
        try {
            const response = await API.getFileTree(path);
            
            if (response.success) {
                this.fileTree.innerHTML = '';
                this.renderTree(response.files, this.fileTree);
            }
        } catch (error) {
            console.error('Failed to load file tree:', error);
        }
    },

    /**
     * ツリーをレンダリング
     */
    renderTree(items, container) {
        items.forEach(item => {
            const itemEl = document.createElement('div');
            itemEl.className = 'file-tree-item';
            
            const nodeEl = document.createElement('div');
            nodeEl.className = 'tree-node';
            nodeEl.dataset.path = item.path;
            nodeEl.dataset.type = item.type;
            
            if (item.type === 'folder') {
                const toggleEl = document.createElement('span');
                toggleEl.className = 'tree-toggle';
                toggleEl.textContent = '▶';
                toggleEl.style.marginLeft = '0';
                
                const iconEl = document.createElement('span');
                iconEl.className = 'tree-icon';
                iconEl.textContent = '📁';
                
                const labelEl = document.createElement('span');
                labelEl.className = 'tree-label';
                labelEl.textContent = item.name;
                
                nodeEl.appendChild(toggleEl);
                nodeEl.appendChild(iconEl);
                nodeEl.appendChild(labelEl);
                
                const childrenEl = document.createElement('div');
                childrenEl.className = 'tree-children';
                
                nodeEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isExpanded = childrenEl.classList.contains('visible');
                    
                    if (isExpanded) {
                        childrenEl.classList.remove('visible');
                        toggleEl.classList.remove('expanded');
                    } else {
                        childrenEl.classList.add('visible');
                        toggleEl.classList.add('expanded');
                        if (childrenEl.children.length === 0) {
                            this.renderTree(item.children || [], childrenEl);
                        }
                    }
                });
                
                itemEl.appendChild(nodeEl);
                itemEl.appendChild(childrenEl);
            } else {
                const iconEl = document.createElement('span');
                iconEl.className = 'tree-icon';
                iconEl.textContent = this.getFileIcon(item.name);
                
                const labelEl = document.createElement('span');
                labelEl.className = 'tree-label';
                labelEl.textContent = item.name;
                
                nodeEl.appendChild(iconEl);
                nodeEl.appendChild(labelEl);
                
                nodeEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectNode(nodeEl);
                    EditorManager.openFile(item.path);
                    TabManager.addTab(item.path, item.name);
                });
                
                nodeEl.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    this.showContextMenu(e, item);
                });
                
                itemEl.appendChild(nodeEl);
            }
            
            container.appendChild(itemEl);
        });
    },

    /**
     * ファイルアイコン取得
     */
    getFileIcon(filename) {
        const icons = {
            'php': '🐘',
            'js': '📜',
            'ts': '📘',
            'html': '🏷️',
            'css': '🎨',
            'json': '{ }',
            'md': '📝',
            'sql': '🗄️',
            'xml': '</>',
            'yaml': '⚙️',
            'py': '🐍',
            'java': '☕'
        };
        const ext = filename.split('.').pop().toLowerCase();
        return icons[ext] || '📄';
    },

    /**
     * ノード選択
     */
    selectNode(nodeEl) {
        if (this.selectedNode) {
            this.selectedNode.classList.remove('active');
        }
        nodeEl.classList.add('active');
        this.selectedNode = nodeEl;
    },

    /**
     * コンテキストメニュー表示
     */
    showContextMenu(event, item) {
        const menu = document.createElement('div');
        menu.className = 'context-menu';
        menu.style.cssText = `
            position: fixed;
            top: ${event.clientY}px;
            left: ${event.clientX}px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 1000;
            min-width: 200px;
        `;
        
        const options = [
            { label: '削除', action: () => this.deleteFile(item.path) },
            { label: '名前変更', action: () => this.renameFile(item.path) }
        ];
        
        options.forEach(opt => {
            const optionEl = document.createElement('div');
            optionEl.style.cssText = 'padding: 8px 12px; cursor: pointer; border-bottom: 1px solid var(--border-color);';
            optionEl.textContent = opt.label;
            optionEl.addEventListener('mouseover', () => optionEl.style.background = 'var(--bg-tertiary)');
            optionEl.addEventListener('mouseout', () => optionEl.style.background = '');
            optionEl.addEventListener('click', () => {
                opt.action();
                menu.remove();
            });
            menu.appendChild(optionEl);
        });
        
        document.body.appendChild(menu);
        document.addEventListener('click', () => menu.remove(), { once: true });
    },

    /**
     * ファイル削除
     */
    async deleteFile(path) {
        if (confirm(`本当に削除しますか? ${path}`)) {
            try {
                const response = await API.deleteFile(path);
                if (response.success) {
                    this.loadTree();
                    TabManager.closeTab(path);
                    UI.showNotification('削除しました', 'success');
                }
            } catch (error) {
                UI.showNotification(`削除に失敗しました: ${error.message}`, 'error');
            }
        }
    },

    /**
     * ファイル名変更
     */
    async renameFile(path) {
        const newName = prompt('新しいファイル名:', path.split('/').pop());
        if (newName) {
            console.log('Rename to:', newName);
        }
    },

    /**
     * ノードをリセット
     */
    reset() {
        this.fileTree.innerHTML = '';
        this.selectedNode = null;
        this.loadTree();
    },

    /**
     * イベントリスナーセットアップ
     */
    setupEventListeners() {
        // 後でここに追加のイベントリスナーを設定
    }
};