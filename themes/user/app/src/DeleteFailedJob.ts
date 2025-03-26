import config from "./Config.ts";
import { BasicResponse, BasicResponseSchema } from "./Queue.ts";

export default async function DeleteFailedJob(jobId: string): Promise<BasicResponse> {
    const requestConfig = {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
            'Http-X-Requested-With': 'XMLHttpRequest',
        },
        body: new URLSearchParams({
            'csrf_token': config.csrfToken,
            'jobId': jobId,
        }),
    };

    try {
        const response = await fetch(config.urlDeleteFailedJob, requestConfig);
        const data = await response.json();
        return BasicResponseSchema.parse(data);
    } catch (error) {
        console.error('Error deleting failed job:', error);
        throw error;
    }
}
