import config from "./Config.ts";
import { BasicResponse, BasicResponseSchema } from "./Queue.ts";

export default async function PurgeAllPendingJobs(queueName: string): Promise<BasicResponse> {
    const requestConfig = {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
            'Http-X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({
            'csrf_token': config.csrfToken,
            'queueName': queueName,
        }),
    };

    try {
        const response = await fetch(config.urlPurgeAllPendingJobs, requestConfig);
        const data = await response.json();
        return BasicResponseSchema.parse(data);
    } catch (error) {
        console.error('Error purging pending jobs:', error);
        throw error;
    }
}
