import { useEffect, useState } from 'react'
import './App.css'
import config from './Config.ts'

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
    queue: string;
    payload: string;
    exception: string;
    failed_at: number;
};

type Queue = {
    queueName: string;
    pendingCount: number
    pending: Array<PendingJob>;
    failedCount: number;
    failed: Array<FailedJob>;
};

type QueueStatusResponse = Array<Queue>;

function App() {
    const [queueStatus, setQueueStatus] = useState<QueueStatusResponse>([]);

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

    const hasFailedJobs: boolean = queueStatus.filter((queue: Queue) => {
        return queue.failedCount > 0;
    }).length > 0;

    const retryJob = function () {}

    return (
        <>
            <div className="panel">
                <div className="panel-heading">
                    <h2>Current Workload</h2>
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
                            </tr>
                            </thead>
                            <tbody>
                            {queueStatus.length > 0 ? (
                                queueStatus.map((queue: Queue) => {
                                    return (
                                        <tr className="app-listing__row">
                                            <td>
                                                {queue.queueName}
                                            </td>
                                            <td>
                                                {queue.pendingCount || 0}
                                            </td>
                                        </tr>
                                    )
                                })
                            ) : (
                                <tr className="app-listing__row">
                                    <td>
                                        default
                                    </td>
                                    <td>
                                        0
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
                        <h2>Failed Jobs</h2>
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
                                {queueStatus.length > 0 && (
                                    queueStatus.map((queue: Queue) => {
                                        return queue.failed.map((failed: FailedJob) => {
                                            return (
                                                <tr className="app-listing__row">
                                                    <td>
                                                        {failed.queue}
                                                    </td>
                                                    <td>
                                                        {failed.failed_at || 0}
                                                    </td>
                                                    <td>
                                                        {failed.exception}
                                                    </td>
                                                    <td>
                                                        <a onClick={retryJob}>Retry</a>
                                                    </td>
                                                </tr>
                                            )
                                        })
                                    })
                                )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}

export default App;
