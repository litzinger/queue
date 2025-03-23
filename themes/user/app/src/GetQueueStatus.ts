import { QueueStatusResponse } from "./Queue.ts";
import config from "./Config.ts";

export default async function GetQueueStatus(): Promise<QueueStatusResponse> {
    const response = await fetch(config.urlQueueStatus, { cache: 'no-store' });
    const result: QueueStatusResponse = await response.json();

    if (response.status !== 200) {
        console.error('Error fetching queue status:', response.statusText);

        return {
            pending: [],
            failed: [],
        };
    }

    return result;
}
