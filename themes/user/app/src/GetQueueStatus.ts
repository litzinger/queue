import { z } from "zod";
import { QueueStatusResponse, QueueStatusResponseSchema } from "./Queue.ts";
import config from "./Config.ts";

export default async function GetQueueStatus(): Promise<QueueStatusResponse> {
    const response = await fetch(config.urlQueueStatus, { cache: 'no-store' });
    const data = await response.json();

    if (response.status !== 200) {
        console.error('Error fetching queue status:', response.statusText);
        return {
            pending: [],
            failed: [],
        };
    }

    try {
        return QueueStatusResponseSchema.parse(data);
    } catch (error) {
        if (error instanceof z.ZodError) {
            console.error('Invalid queue status response:', error.errors);
        } else {
            console.error('Error validating queue status:', error);
        }
        return {
            pending: [],
            failed: [],
        };
    }
}
