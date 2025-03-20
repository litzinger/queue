import { useEffect, useState, Suspense } from 'react'
import './App.css'
import config from './Config.ts'
import Loading from "./loading.tsx";

type PendingJob = {
    id: string;
    queue: string;
    payload: string;
    attempts: number;
    reserved_at: number;
    available_at: number;
    created_at: number;
};

type FailedJob = {
    id: string;
    uuid: string;
    queue: string;
    payload: string;
    exception: string;
    failed_at: number;
};

type PendingQueue = {
    queueName: string;
    count: number;
    jobs: Array<PendingJob>;
};

type FailedQueue = {
    queueName: string;
    count: number;
    jobs: Array<FailedJob>;
}

type QueueStatusResponse = {
    pending: Array<PendingQueue>;
    failed: Array<FailedQueue>;
};

function App() {
    const [queueStatus, setQueueStatus] = useState<QueueStatusResponse>({
        pending: [],
        failed: [],
    });

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch(config.urlQueueStatus, { cache: 'no-store' });
                const result: QueueStatusResponse = await response.json();

                setQueueStatus(result);
            } catch (error) {
                console.error('Error fetching queue status:', error);
            }
        };

        const interval = setInterval(fetchData, 1000);

        fetchData();

        return () => clearInterval(interval);
    }, []);

    const hasPendingJobs: boolean = queueStatus.pending.filter((queue: PendingQueue) => {
        return queue.count > 0;
    }).length > 0;

    const hasFailedJobs: boolean = queueStatus.failed.filter((queue: FailedQueue) => {
        return queue.count > 0;
    }).length > 0;

    const retryFailedJob = async (jobId: string) => {
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

        return await fetch(config.urlRetryFailedJob, requestConfig)
            .then(res => res.json())
            .then(
                (result) => {
                    return result;
                },
                (error) => {
                    console.log(error);
                }
            );
    };

    const purgeAllPendingJobs = async (queueName: string) => {
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
    };

    return (
        <Suspense fallback={<Loading />}>
            <div className="panel">
                <div className="panel-heading">
                    <div className="title-bar title-bar--large">
                        <h3 className="title-bar__title">Current Workload</h3>

                        <div className="title-bar__extra-tools text-light">
                            Driver: {config.queueDriver}
                        </div>
                    </div>
                </div>
                <div className="panel-body panel-body__table">
                    <div className="table-responsive table-responsive--collapsible">
                        <table>
                            <thead>
                            <tr className="app-listing__row app-listing__row--head">
                                <th>
                                    Queue
                                </th>
                                <th>
                                    Jobs
                                </th>
                                <th className="text-right">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            {hasPendingJobs ? (
                                queueStatus.pending.map((queue: PendingQueue) => {
                                    return (
                                        <>
                                            <tr className="app-listing__row" key={queue.queueName}>
                                                <td>
                                                    {queue.queueName}
                                                </td>
                                                <td>
                                                    {queue.count || 0}
                                                </td>
                                                <td className="text-right">
                                                    <div className="button-group button-group-xsmall inline-block">
                                                        <button
                                                            onClick={() => purgeAllPendingJobs(queue.queueName)}
                                                            className="button button--default fas fa-trash"
                                                        >
                                                            <span className="hidden">Purge all jobs in {queue.queueName}</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            {/*{queue.jobs.map((job: PendingJob) => {*/}
                                            {/*    const payload = JSON.parse(job.payload);*/}

                                            {/*    return (*/}
                                            {/*        <tr className="app-listing__row" key={job.id}>*/}
                                            {/*            <td colSpan={2}>*/}
                                            {/*                {payload.displayName ?? 'n/a'}*/}
                                            {/*            </td>*/}
                                            {/*            <td>*/}
                                            {/*                Created:*/}
                                            {/*            </td>*/}
                                            {/*        </tr>*/}
                                            {/*    );*/}
                                            {/*})}*/}
                                        </>
                                    )
                                })
                            ) : (
                                <tr className="app-listing__row">
                                    <td colSpan={3}>
                                        No jobs found.
                                    </td>
                                </tr>
                            )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {hasFailedJobs && (
                <div className="panel">
                    <div className="panel-heading">
                        <div className="title-bar title-bar--large">
                            <h3 className="title-bar__title">Failed Jobs</h3>
                        </div>
                    </div>
                    <div className="panel-body panel-body__table">
                        <div className="table-responsive table-responsive--collapsible">
                            <table>
                                <thead>
                                <tr className="app-listing__row app-listing__row--head">
                                    <th>
                                        Queue
                                    </th>
                                    <th>
                                        Failed At
                                    </th>
                                    <th>
                                        Exception
                                    </th>
                                    <th>
                                        Retry
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    {queueStatus.failed.map((queue: FailedQueue) => {
                                        return queue.jobs.map((failed: FailedJob) => {
                                            const payload = JSON.parse(failed.payload);

                                            return (
                                                <tr className="app-listing__row" key={failed.uuid}>
                                                    <td className="w-full">
                                                        {failed.queue}
                                                    </td>
                                                    <td className="w-full">
                                                        {failed.failed_at || 0}
                                                    </td>
                                                    <td>
                                                        {payload.displayName ?? ''}:
                                                        <code>{failed.exception}</code>
                                                    </td>
                                                    <td>
                                                        <div className="button-group button-group-xsmall">
                                                            <button
                                                                onClick={() => retryFailedJob(failed.uuid)}
                                                                className="button button--default fas fa-recycle"
                                                            >
                                                                <span className="hidden">Retry Job</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            )
                                        })
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}
        </Suspense>
    );
}

export default App;
