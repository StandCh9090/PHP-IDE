/**
 * UI ユーティリティ
 */
const UI = {
    /**
     * 通知表示
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 50px;
            right: 20px;
            padding: 12px 16px;
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;

        if (type === 'error') {
            notification.style.borderColor = 'var(--danger-color)';
        } else if (type === 'success') {
            notification.style.borderColor = 'var(--success-color)';
        }

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    },

    /**
     * ダイアログ表示
     */
    showDialog(title, message, buttons = ['OK']) {
        return new Promise((resolve) => {
            const dialog = document.createElement('div');
            dialog.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10001;
            `;

            const content = document.createElement('div');
            content.style.cssText = `
                background: var(--bg-primary);
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 20px;
                min-width: 300px;
                box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            `;

            const titleEl = document.createElement('h2');
            titleEl.textContent = title;
            titleEl.style.marginBottom = '12px';

            const messageEl = document.createElement('p');
            messageEl.textContent = message;
            messageEl.style.marginBottom = '20px';
            messageEl.style.color = 'var(--text-secondary)';

            const buttonsContainer = document.createElement('div');
            buttonsContainer.style.display = 'flex';
            buttonsContainer.style.gap = '8px';
            buttonsContainer.style.justifyContent = 'flex-end';

            buttons.forEach((buttonText, index) => {
                const btn = document.createElement('button');
                btn.textContent = buttonText;
                btn.style.cssText = `
                    padding: 8px 16px;
                    background: var(--accent-color);
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                `;
                btn.addEventListener('click', () => {
                    dialog.remove();
                    resolve(index);
                });
                buttonsContainer.appendChild(btn);
            });

            content.appendChild(titleEl);
            content.appendChild(messageEl);
            content.appendChild(buttonsContainer);
            dialog.appendChild(content);
            document.body.appendChild(dialog);
        });
    },

    /**
     * サイドバー切り替え
     */
    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.style.display = sidebar.style.display === 'none' ? 'flex' : 'none';
    },

    /**
     * パネルを切り替え
     */
    switchPanel(panelName) {
        const panels = document.querySelectorAll('.panel');
        const tabs = document.querySelectorAll('.panel-tab');

        panels.forEach(panel => panel.classList.remove('active'));
        tabs.forEach(tab => tab.classList.remove('active'));

        document.getElementById(`${panelName}Panel`).classList.add('active');
        document.querySelector(`[data-panel="${panelName}"]`).classList.add('active');
    }
};

// アニメーション定義
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);