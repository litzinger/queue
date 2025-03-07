<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobQueued;

class JobQueuedListener extends AbstractListener
{
    public function handle(JobQueued $event)
    {
        $config = ee()->config->item('queue') ?: [];

        if (get_bool_from_string($config['enable_detailed_logging'])) {
            ee('queue:Logger')->developer(sprintf(
                '[Queue] job queued with %s',
                json_encode($event->job->payload())
            ));
        }
    }
}
