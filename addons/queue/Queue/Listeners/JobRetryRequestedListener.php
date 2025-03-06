<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobRetryRequested;

class JobRetryRequestedListener extends AbstractListener
{
    public function handle(JobRetryRequested $event)
    {
        if (bool_config_item('queue_enable_detailed_logging')) {
            ee('queue:Logger')->developer(sprintf(
                '[Queue] job retried with %s',
                json_encode($event->job->job->payload())
            ));
        }
    }
}
