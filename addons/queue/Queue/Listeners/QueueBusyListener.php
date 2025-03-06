<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\QueueBusy;

class QueueBusyListener extends AbstractListener
{
    public function handle(QueueBusy $event) {}
}
