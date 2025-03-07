declare global {
    interface Window {
        APP_CONFIG: {
            urlQueueStatus: string;
        };
    }
}

const config = window.APP_CONFIG || { urlQueueStatus: "" };

export default config;
