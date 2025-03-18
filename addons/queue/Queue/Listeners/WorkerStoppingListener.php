<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\WorkerStopping;

class WorkerStoppingListener extends AbstractListener
{
    public function handle(WorkerStopping $event) {}
}
