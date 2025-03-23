import config from './Config.ts';
import { FailedJob } from './Queue.ts';

export default async function GetFailedJob(jobId: string): Promise<FailedJob> {
    const requestConfig = {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Http-X-Requested-With': 'XMLHttpRequest',
        },
    };

    const url = `${config.urlGetFailedJob}&jobId=${jobId}`;

    return fetch(url, requestConfig)
        .then((res) => res.json())
        .catch((error) => {
            console.error('Fetch error:', error);
            throw error;
        });
}
