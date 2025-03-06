<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobFailed;
use BoldMinded\Queue\Model\ImportStatus;

class JobFailedListener
{
    public function handle(JobFailed $event)
    {
        ee('queue:Logger')->developer($event->exception->getMessage());
    }
}
