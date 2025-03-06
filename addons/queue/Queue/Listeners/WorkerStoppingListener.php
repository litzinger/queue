<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\WorkerStopping;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Worker;
use BoldMinded\Queue\Model\ImportStatus;

class WorkerStoppingListener extends AbstractListener
{
    public function handle(WorkerStopping $event) {}
}
