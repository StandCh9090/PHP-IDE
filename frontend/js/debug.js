/**
 * \u30c7\u30d0\u30c3\u30ac\u30fc\u7ba1\u7406
 */
const DebugManager = {
    breakpoints: new Map(),
    isDebugging: false,
    currentLine: null,

    /**
     * \u30d6\u30ec\u30fc\u30af\u30dd\u30a4\u30f3\u30c8\u3092\u8a2d\u5b9a
     */
    async setBreakpoint(file, line) {
        try {
            const response = await API.post('/debug/breakpoint', {
                file: file,
                line: line
            });

            if (response.success) {
                const key = `${file}:${line}`;
                this.breakpoints.set(key, {
                    file: file,
                    line: line,
                    id: response.breakpointId
                });
                UI.showNotification(`Breakpoint set at line ${line}`, 'success');
                this.updateBreakpointUI();
            }
        } catch (error) {
            console.error('Set breakpoint error:', error);
        }
    },

    /**
     * \u30d6\u30ec\u30fc\u30af\u30dd\u30a4\u30f3\u30c8\u3092\u524a\u9664
     */
    async removeBreakpoint(file, line) {
        const key = `${file}:${line}`;
        const bp = this.breakpoints.get(key);

        if (!bp) return;

        try {
            const response = await API.post('/debug/breakpoint/remove', {
                breakpointId: bp.id
            });

            if (response.success) {
                this.breakpoints.delete(key);
                UI.showNotification('Breakpoint removed', 'success');
                this.updateBreakpointUI();
            }
        } catch (error) {
            console.error('Remove breakpoint error:', error);
        }
    },

    /**
     * \u5b9f\u884c\u518d\u958b
     */
    async resume() {
        try {
            await API.post('/debug/resume', {});
            this.isDebugging = false;
            UI.showNotification('Execution resumed', 'info');
        } catch (error) {
            console.error('Resume error:', error);
        }
    },

    /**
     * \u5b9f\u884c\u3092\u4e00\u6b87\u6b62\u3081
     */
    async pause() {
        try {
            await API.post('/debug/pause', {});
            this.isDebugging = true;
            UI.showNotification('Execution paused', 'info');
        } catch (error) {
            console.error('Pause error:', error);
        }
    },

    /**
     * \u305d\u308c\u3063\u305d\u308c\u7684\u306b\u5b9f\u884c
     */
    async step() {
        try {
            const response = await API.post('/debug/step', {});
            if (response.success) {
                // UI \u66f4\u65b0
            }
        } catch (error) {
            console.error('Step error:', error);
        }
    },

    /**
     * UI \u3092\u66f4\u65b0
     */
    updateBreakpointUI() {
        // \u30a8\u30c7\u30a3\u30bf\u30fc\u5185\u306b\u30d6\u30ec\u30fc\u30af\u30dd\u30a4\u30f3\u30c8\u3092\u8868\u793a
        // (\u5b9f\u88c5\u8a73\u7b80\u7684)
    }
};
