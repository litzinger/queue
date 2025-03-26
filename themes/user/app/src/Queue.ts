import { z } from "zod";

export const BasicResponseSchema = z.object({
    success: z.boolean(),
});

export type BasicResponse = z.infer<typeof BasicResponseSchema>;

export const PendingJobSchema = z.object({
    id: z.string(),
    queue: z.string(),
    payload: z.any(),
    attempts: z.number(),
    reserved_at: z.number(),
    available_at: z.number(),
    created_at: z.number(),
});

export const FailedJobPayloadSchema = z.object({
    uuid: z.string(),
    displayName: z.string(),
    job: z.string(),
    maxTries: z.number().nullable(),
    maxExceptions: z.number().nullable(),
    failOnTimeout: z.literal(false).nullable(),
    backoff: z.number().nullable(),
    timeout: z.number().nullable(),
    data: z.any(),
});

export const FailedJobSchema = z.object({
    id: z.number(),
    uuid: z.string(),
    queue: z.string(),
    payload: FailedJobPayloadSchema,
    exception: z.string(),
    failed_at: z.string(),
});

export const PendingQueueSchema = z.object({
    queueName: z.string(),
    count: z.number(),
    jobs: z.array(PendingJobSchema).default([]),
});

export const FailedQueueSchema = z.object({
    queueName: z.string(),
    count: z.number(),
    jobs: z.array(FailedJobSchema).default([]),
});

export const QueueStatusResponseSchema = z.object({
    pending: z.array(PendingQueueSchema).default([]),
    failed: z.array(FailedQueueSchema).default([]),
});

export type PendingJob = z.infer<typeof PendingJobSchema>;
export type FailedJobPayload = z.infer<typeof FailedJobPayloadSchema>;
export type FailedJob = z.infer<typeof FailedJobSchema>;
export type PendingQueue = z.infer<typeof PendingQueueSchema>;
export type FailedQueue = z.infer<typeof FailedQueueSchema>;
export type QueueStatusResponse = z.infer<typeof QueueStatusResponseSchema>;

export function createEmptyFailedJob(): FailedJob {
    return {
        id: 0,
        uuid: '',
        queue: '',
        payload: {
            uuid: '',
            displayName: '',
            job: '',
            maxTries: 0,
            maxExceptions: 0,
            failOnTimeout: false,
            backoff: 0,
            timeout: 0,
            data: '',
        },
        exception: '',
        failed_at: '',
    };
}
