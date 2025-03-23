import React, {useEffect, useState} from 'react';
import { useParams, Link } from 'react-router';
import GetFailedJob from "./GetFailedJob.ts";
import RetryFailedJob from './RetryFailedJob.ts';
import { FailedJob } from "./Queue.ts";

export default function ViewFailedJob (): React.ReactElement {
    const { jobId } = useParams<{ jobId: string }>();

    if (!jobId) {
        return (
            <>Missing jobId</>
        );
    }

    const [loading, setLoading] = useState(true);
    const [job, setJob] = useState<FailedJob>({
        id: '',
        uuid: '',
        queue: '',
        payload: {
            uuid: '',
            displayName: '',
            job: '',
            exception: '',
            maxTries: 0,
            maxExceptions: 0,
            failOnTimeout: false,
            backoff: 0,
            timeout: 0,
            data: '',
        },
        exception: '',
        failed_at: 0,
    });

    useEffect(() => {
        if (!jobId) {
            return;
        }

        setLoading(true);

        GetFailedJob(jobId)
            .then((data) => {
                console.log(data);
                setJob(data);
            })
            .finally(() => setLoading(false));
    }, [jobId]);

    if (loading) {
        return <p>Loading...</p>;
    }

    return (
        <div className="panel">
            <div className="panel-heading">
                <div className="title-bar title-bar--large">
                    <h3 className="title-bar__title">{job.payload.displayName || 'n/a'}</h3>

                    <div className="title-bar__extra-tools text-light">
                        <button
                            onClick={() => RetryFailedJob(job.uuid)}
                            className="button button--default fas fa-recycle"
                            title="Retry Job"
                        >
                            <span className="hidden">Retry Job</span>
                        </button>
                    </div>
                </div>
            </div>
            <div className="panel-body">
                <code>{job.exception}</code>
            </div>
            <div className="panel-footer">
                <Link
                    to="/"
                    className="button button--primary"
                >
                    Back
                </Link>
            </div>
        </div>
    );
}
