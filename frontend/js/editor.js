/**
 * Monaco Editor管理
 */
const EditorManager = {
    editor: null,
    currentFile: null,
    unsavedFiles: new Map(),

    /**
     * エディター初期化
     */
    async init() {
        require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' } });
        
        return new Promise((resolve) => {
            require(['vs/editor/editor.main'], () => {
                this.editor = monaco.editor.create(
                    document.getElementById('editor'),
                    {
                        value: '// PHP-IDEへようこそ!\n// ファイルを選択してコーディングを始めましょう。',
                        language: 'javascript',
                        theme: CONFIG.EDITOR.theme === 'vs-dark' ? 'vs-dark' : 'vs',
                        fontSize: CONFIG.EDITOR.fontSize,
                        fontFamily: CONFIG.EDITOR.fontFamily,
                        tabSize: CONFIG.EDITOR.tabSize,
                        insertSpaces: CONFIG.EDITOR.insertSpaces,
                        wordWrap: CONFIG.EDITOR.wordWrap,
                        minimap: CONFIG.EDITOR.minimap,
                        automaticLayout: true,
                        scrollBeyondLastLine: false,
                        contextmenu: true,
                        lineNumbers: 'on',
                        glyphMargin: true,
                        folding: true,
                        foldingHighlight: true,
                        renderLineHighlight: 'all',
                        mouseWheelZoom: true
                    }
                );

                // 変更検出
                this.editor.getModel().onDidChangeContent(() => {
                    if (this.currentFile) {
                        this.markAsUnsaved(this.currentFile);
                    }
                });

                resolve();
            });
        });
    },

    /**
     * ファイル開く
     */
    async openFile(filePath) {
        try {
            const response = await API.getFile(filePath);
            
            if (response.success) {
                this.currentFile = filePath;
                const language = this.detectLanguage(filePath);
                
                let model = monaco.editor.getModel(monaco.Uri.parse(`file://${filePath}`));
                if (!model) {
                    model = monaco.editor.createModel(response.content, language, monaco.Uri.parse(`file://${filePath}`));
                } else {
                    model.setValue(response.content);
                }
                
                this.editor.setModel(model);
                this.clearUnsavedFlag(filePath);
                
                console.log(`Opened file: ${filePath}`);
            }
        } catch (error) {
            console.error('Failed to open file:', error);
            UI.showNotification(`ファイルを開けません: ${error.message}`, 'error');
        }
    },

    /**
     * ファイル保存
     */
    async saveFile(filePath = this.currentFile) {
        if (!filePath) return;

        try {
            const content = this.editor.getValue();
            const response = await API.saveFile(filePath, content);
            
            if (response.success) {
                this.clearUnsavedFlag(filePath);
                UI.showNotification(`保存しました: ${filePath}`, 'success');
                console.log(`Saved file: ${filePath}`);
            }
        } catch (error) {
            console.error('Failed to save file:', error);
            UI.showNotification(`保存に失敗しました: ${error.message}`, 'error');
        }
    },

    /**
     * 言語自動検出
     */
    detectLanguage(filePath) {
        const extension = filePath.split('.').pop().toLowerCase();
        const languageMap = {
            'php': 'php',
            'js': 'javascript',
            'ts': 'typescript',
            'jsx': 'javascript',
            'tsx': 'typescript',
            'html': 'html',
            'htm': 'html',
            'css': 'css',
            'scss': 'scss',
            'json': 'json',
            'xml': 'xml',
            'yaml': 'yaml',
            'yml': 'yaml',
            'sql': 'sql',
            'py': 'python',
            'java': 'java'
        };
        return languageMap[extension] || 'plaintext';
    },

    /**
     * 未保存としてマーク
     */
    markAsUnsaved(filePath) {
        if (!this.unsavedFiles.has(filePath)) {
            this.unsavedFiles.set(filePath, true);
            TabManager.updateTabStatus(filePath);
        }
    },

    /**
     * 未保存フラグをクリア
     */
    clearUnsavedFlag(filePath) {
        this.unsavedFiles.delete(filePath);
        TabManager.updateTabStatus(filePath);
    },

    /**
     * テーマ変更
     */
    setTheme(theme) {
        if (this.editor) {
            monaco.editor.setTheme(theme === 'dark' ? 'vs-dark' : 'vs');
        }
    },

    /**
     * フォントサイズ変更
     */
    setFontSize(size) {
        if (this.editor) {
            this.editor.updateOptions({ fontSize: size });
        }
    },

    /**
     * 現在のコンテンツ取得
     */
    getContent() {
        return this.editor ? this.editor.getValue() : '';
    },

    /**
     * コンテンツ設定
     */
    setContent(content) {
        if (this.editor) {
            this.editor.setValue(content);
        }
    }
};