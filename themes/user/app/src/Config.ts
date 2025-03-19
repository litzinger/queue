declare global {
    interface Window {
        APP_CONFIG: {
            urlQueueStatus: string;
            urlPurgeAllPendingJobs: string;
            urlRetryFailedJob: string;
            csrfToken: string;
        };
    }
}

const config = window.APP_CONFIG || { urlQueueStatus: "" };

export default config;
