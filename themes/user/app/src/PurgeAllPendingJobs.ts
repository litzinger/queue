import config from "./Config.ts";
import { BasicResponse } from "./BasicResponse.ts";

export default async function DeleteFailedJob(queueName: string): Promise<BasicResponse> {
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

    return await fetch(config.urlPurgeAllPendingJobs, requestConfig)
        .then(res => res.json())
        .then(
            (result) => {
                return result;
            },
            (error) => {
                console.log(error);
            }
        );
}
