<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobQueued;

class JobQueuedListener
{
    public function handle(JobQueued $event)
    {
        if (bool_config_item('queue_enable_detailed_logging')) {
            ee('queue:Logger')->developer(sprintf(
                '[Queue] job queued with %s',
                json_encode($event->job->payload())
            ));
        }
    }
}
