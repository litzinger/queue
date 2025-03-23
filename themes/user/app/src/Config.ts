declare global {
    interface Window {
        APP_CONFIG: {
            urlBase: string;
            urlQueueStatus: string;
            urlPurgeAllPendingJobs: string;
            urlRetryFailedJob: string;
            urlDeleteFailedJob: string;
            urlGetFailedJob: string;
            csrfToken: string;
            queueDriver: string;
        };
    }
}

const config = window.APP_CONFIG || { urlQueueStatus: "" };

export default config;
