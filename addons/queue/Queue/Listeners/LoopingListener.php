<?php

namespace BoldMinded\Queue\Queue\Listeners;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\Looping;

class LoopingListener extends AbstractListener
{
    public function handle(Looping $event) {}
}
