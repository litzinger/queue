<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobProcessing;

class JobProcessingListener
{
    public function handle(JobProcessing $event)
    {
        ee('queue:Logger')->developer($event->job->getJobId());
    }
}
