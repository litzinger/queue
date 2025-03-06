<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobFailed;
use BoldMinded\Queue\Model\ImportStatus;

class JobFailedListener extends AbstractListener
{
    public function handle(JobFailed $event)
    {
        ee('queue:Logger')->developer(sprintf(
            '[Queue] Job Failed %s',
            $event->exception->getMessage()
        ));
    }
}
