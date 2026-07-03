/**
 * ターミナル管理
 */
const TerminalManager = {
    terminal: null,
    terminalInput: null,
    commandHistory: [],
    historyIndex: -1,

    /**
     * 初期化
     */
    init() {
        this.terminal = document.getElementById('terminal');
        this.terminalInput = document.getElementById('terminalInput');
        
        this.terminalInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.executeCommand(this.terminalInput.value);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.showPreviousCommand();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.showNextCommand();
            }
        });
        
        this.print('PHP-IDE Terminal v1.0');
        this.print('Type "help" for available commands');
    },

    /**
     * コマンド実行
     */
    async executeCommand(command) {
        command = command.trim();
        if (!command) return;

        this.print(`$ ${command}`);
        this.commandHistory.push(command);
        this.historyIndex = -1;
        this.terminalInput.value = '';

        try {
            const response = await API.post('/terminal/execute', { command });
            if (response.success) {
                this.print(response.output);
            } else {
                this.print(`Error: ${response.error}`, 'error');
            }
        } catch (error) {
            this.print(`Error: ${error.message}`, 'error');
        }
    },

    /**
     * 出力を印字
     */
    print(text, type = 'normal') {
        const line = document.createElement('div');
        line.className = `terminal-line terminal-${type}`;
        line.textContent = text;
        this.terminal.appendChild(line);
        this.terminal.scrollTop = this.terminal.scrollHeight;
    },

    /**
     * 前のコマンドを表示
     */
    showPreviousCommand() {
        if (this.historyIndex < this.commandHistory.length - 1) {
            this.historyIndex++;
            this.terminalInput.value = this.commandHistory[
                this.commandHistory.length - 1 - this.historyIndex
            ];
        }
    },

    /**
     * 次のコマンドを表示
     */
    showNextCommand() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.terminalInput.value = this.commandHistory[
                this.commandHistory.length - 1 - this.historyIndex
            ];
        } else if (this.historyIndex === 0) {
            this.historyIndex = -1;
            this.terminalInput.value = '';
        }
    },

    /**
     * ターミナルをクリア
     */
    clear() {
        this.terminal.innerHTML = '';
    }
};