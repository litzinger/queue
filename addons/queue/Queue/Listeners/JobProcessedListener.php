<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobProcessed;

class JobProcessedListener
{
    public function handle(JobProcessed $jobProcessed)
    {
        // $item = $jobProcessed->job->payload();
        ee('queue:Logger')->developer($jobProcessed->job->getJobId());
    }
}
