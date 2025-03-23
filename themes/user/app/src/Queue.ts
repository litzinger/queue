export type PendingJob = {
    id: string;
    queue: string;
    payload: string;
    attempts: number;
    reserved_at: number;
    available_at: number;
    created_at: number;
};

export type FailedJobPayload = {
    uuid: string;
    displayName: string;
    job: string;
    exception: string;
    maxTries: number;
    maxExceptions: number;
    failOnTimeout: false;
    backoff: number;
    timeout: number;
    data: string;
};

export type FailedJob = {
    id: string;
    uuid: string;
    queue: string;
    payload: FailedJobPayload;
    exception: string;
    failed_at: number;
};

export type PendingQueue = {
    queueName: string;
    count: number;
    jobs: Array<PendingJob>;
};

export type FailedQueue = {
    queueName: string;
    count: number;
    jobs: Array<FailedJob>;
}

export type QueueStatusResponse = {
    pending: Array<PendingQueue>;
    failed: Array<FailedQueue>;
};
