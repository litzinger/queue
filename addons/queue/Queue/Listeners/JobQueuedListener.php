<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobQueued;

class JobQueuedListener
{
    public function handle(JobQueued $event) {}
}
