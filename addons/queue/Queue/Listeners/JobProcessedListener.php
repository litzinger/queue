<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobProcessed;

class JobProcessedListener extends AbstractListener
{
    public function handle(JobProcessed $event)
    {
        $config = ee()->config->item('queue') ?: [];

        if (get_bool_from_string($config['enable_detailed_logging'])) {
            ee('queue:Logger')->developer(sprintf(
                '[Queue] job %d processed with: %s',
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
