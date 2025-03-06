<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobExceptionOccurred;

class JobExceptionOccurredListener
{
    public function handle(JobExceptionOccurred $event)
    {
        ee('queue:Logger')->developer($event->exception->getMessage());
    }
}
