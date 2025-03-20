declare global {
    interface Window {
        APP_CONFIG: {
            urlQueueStatus: string;
            urlPurgeAllPendingJobs: string;
            urlRetryFailedJob: string;
            csrfToken: string;
            queueDriver: string;
        };
    }
}

const config = window.APP_CONFIG || { urlQueueStatus: "" };

export default config;
