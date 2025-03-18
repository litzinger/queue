<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobRetryRequested;

class JobRetryRequestedListener extends AbstractListener
{
    public function handle(JobRetryRequested $event)
    {
        $config = ee()->config->item('queue') ?: [];

        if (get_bool_from_string($config['enable_detailed_logging'] ?? false)) {
            ee('queue:Logger')->developer(sprintf(
                '[Queue] job retried with %s',
                json_encode($event->job->job->payload())
            ));
        }
    }
}
