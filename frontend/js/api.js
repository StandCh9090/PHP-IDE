/**
 * API通信ユーティリティ
 */
const API = {
    /**
     * GETリクエスト
     */
    async get(endpoint) {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('GET Error:', error);
            throw error;
        }
    },

    /**
     * POSTリクエスト
     */
    async post(endpoint, data) {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('POST Error:', error);
            throw error;
        }
    },

    /**
     * PUTリクエスト
     */
    async put(endpoint, data) {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('PUT Error:', error);
            throw error;
        }
    },

    /**
     * DELETEリクエスト
     */
    async delete(endpoint) {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('DELETE Error:', error);
            throw error;
        }
    },

    /**
     * ファイル一覧取得
     */
    async getFileTree(path = '/') {
        return this.get(`/files?path=${encodeURIComponent(path)}`);
    },

    /**
     * ファイル内容取得
     */
    async getFile(path) {
        return this.get(`/file?path=${encodeURIComponent(path)}`);
    },

    /**
     * ファイル保存
     */
    async saveFile(path, content) {
        return this.post('/file/save', { path, content });
    },

    /**
     * ファイル削除
     */
    async deleteFile(path) {
        return this.delete(`/file?path=${encodeURIComponent(path)}`);
    },

    /**
     * フォルダ作成
     */
    async createFolder(path) {
        return this.post('/folder/create', { path });
    },

    /**
     * ファイル作成
     */
    async createFile(path, content = '') {
        return this.post('/file/create', { path, content });
    }
};