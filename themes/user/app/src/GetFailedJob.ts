import config from './Config.ts';
import { FailedJob, FailedJobSchema } from './Queue.ts';

export default async function GetFailedJob(jobId: string): Promise<FailedJob> {
    const requestConfig = {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Http-X-Requested-With': 'XMLHttpRequest',
        },
    };

    const url = `${config.urlGetFailedJob}&jobId=${jobId}`;

    try {
        const response = await fetch(url, requestConfig);
        const data = await response.json();
        return FailedJobSchema.parse(data);
    } catch (error) {
        console.error('Error fetching failed job:', error);
        throw error;
    }
}
