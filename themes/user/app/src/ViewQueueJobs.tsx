import { useEffect, useState } from 'react'
import { Link } from 'react-router';
import {
    FailedJob,
    FailedQueue,
    PendingQueue,
    QueueStatusResponse
} from './Queue.ts';
import GetQueueStatus from './GetQueueStatus.ts';
import config from './Config.ts';
import PurgeAllPendingJobs from './PurgeAllPendingJobs.ts';
import RetryFailedJob from './RetryFailedJob.ts';
import DeleteFailedJob from './DeleteFailedJob.ts';

export default function ViewQueueJobs() {
    const [queueStatus, setQueueStatus] = useState<QueueStatusResponse>({
        pending: [],
        failed: [],
    });

    useEffect(() => {
        const fetchData = async () => {
            try {
                const result = await GetQueueStatus();

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

    return (
        <>
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
                                                            onClick={() => PurgeAllPendingJobs(queue.queueName)}
                                                            className="button button--default fas fa-trash"
                                                            title="Purge all"
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
                                        Job
                                    </th>
                                    <th>
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                {queueStatus.failed.map((queue: FailedQueue) => {
                                    return queue.jobs.map((failed: FailedJob) => {
                                        return (
                                            <tr className="app-listing__row" key={failed.uuid}>
                                                <td className="w-full">
                                                    {failed.queue}
                                                </td>
                                                <td className="w-full">
                                                    {failed.failed_at || 0}
                                                </td>
                                                <td>
                                                    <code>{failed.payload.displayName ?? ''}</code>
                                                </td>
                                                <td className="w-full">
                                                    <div className="button-group button-group-xsmall w-full">
                                                        <Link
                                                            to={`/failed-job/${failed.uuid}`}
                                                            className="button button--default fas fa-eye"
                                                        >
                                                            <span className="hidden">View Job</span>
                                                        </Link>
                                                        <button
                                                            onClick={() => RetryFailedJob(failed.uuid)}
                                                            className="button button--default fas fa-recycle"
                                                            title="Retry Job"
                                                        >
                                                            <span className="hidden">Retry Job</span>
                                                        </button>
                                                        <button
                                                            onClick={() => DeleteFailedJob(failed.uuid)}
                                                            className="button button--default fas fa-trash"
                                                            title="Delete Job"
                                                        >
                                                            <span className="hidden">Delete Job</span>
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
        </>
    );
}
