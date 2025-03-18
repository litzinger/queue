<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobFailed;

class JobFailedListener extends AbstractListener
{
    public function handle(JobFailed $event)
    {
        ee('queue:Logger')->developer(sprintf(
            '[Queue] Job Failed: %s',
            $event->exception->getMessage()
        ));

        $container = ee('queue:QueueManager')->getContainer();
        $container['queue.failer']->log(
            $event->connectionName,
            $event->job->getQueue(),
            $event->job->getRawBody(),
            $event->exception
        );
    }
}
