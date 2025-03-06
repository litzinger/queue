<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobProcessed;

class JobProcessedListener
{
    public function handle(JobProcessed $event)
    {
        if (bool_config_item('queue_enable_detailed_logging')) {
            ee('queue:Logger')->developer(sprintf(
                '[Queue] job %d processed with %s',
                $event->job->getJobId(),
                json_encode($event->job->payload())
            ));

            return;
        }

        ee('queue:Logger')->developer(sprintf(
            '[Queue] job %d processed',
            $event->job->getJobId()
        ));
    }
}
