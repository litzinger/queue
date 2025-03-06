<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobRetryRequested;

class JobRetryRequestedListener
{
    public function handle(JobRetryRequested $event) {}
}
