/**
 * Git\u7d71\u7d71\u6a5f\u80fd
 */
const GitManager = {
    isInitialized: false,
    commits: [],

    /**
     * \u521d\u671f\u5316
     */
    async init() {
        try {
            const response = await API.post('/git/init', {});
            if (response.success) {
                this.isInitialized = true;
                UI.showNotification('Git repository initialized', 'success');
            }
        } catch (error) {
            console.error('Git init error:', error);
        }
    },

    /**
     * \u30b9\u30c6\u30fc\u30bf\u30b9\u3092\u53d6\u5f97
     */
    async getStatus() {
        try {
            const response = await API.get('/git/status');
            if (response.success) {
                return response.files;
            }
        } catch (error) {
            console.error('Git status error:', error);
        }
        return [];
    },

    /**
     * \u30b3\u30df\u30c3\u30c8
     */
    async commit(message) {
        if (!message) {
            UI.showNotification('Commit message is required', 'error');
            return;
        }

        try {
            const response = await API.post('/git/commit', { message });
            if (response.success) {
                UI.showNotification('Committed successfully', 'success');
                this.getLog();
            }
        } catch (error) {
            console.error('Git commit error:', error);
            UI.showNotification('Commit failed: ' + error.message, 'error');
        }
    },

    /**
     * \u30ed\u30b0\u3092\u53d6\u5f97
     */
    async getLog() {
        try {
            const response = await API.get('/git/log?limit=20');
            if (response.success) {
                this.commits = response.commits;
                return response.commits;
            }
        } catch (error) {
            console.error('Git log error:', error);
        }
        return [];
    }
};
