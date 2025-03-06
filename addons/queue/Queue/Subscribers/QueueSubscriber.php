<?php

namespace BoldMinded\Queue\Queue\Subscribers;

use BoldMinded\Queue\Dependency\Illuminate\Events\Dispatcher;

class QueueSubscriber
{
    /**
     * @param Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\WorkerStopping',
            'BoldMinded\Queue\Queue\Listeners\WorkerStoppingListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobProcessed',
            'BoldMinded\Queue\Queue\Listeners\JobProcessedListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobProcessing',
            'BoldMinded\Queue\Queue\Listeners\JobProcessingListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobFailed',
            'BoldMinded\Queue\Queue\Listeners\JobFailedListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobRetryRequested',
            'BoldMinded\Queue\Queue\Listeners\JobRetryRequestedListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobExceptionOccurred',
            'BoldMinded\Queue\Queue\Listeners\JobExceptionOccurredListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobQueued',
            'BoldMinded\Queue\Queue\Listeners\JobQueuedListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\QueueBusy',
            'BoldMinded\Queue\Queue\Listeners\QueueBusyListener@handle'
        );

        $events->listen(
            'BoldMinded\Queue\Dependency\Illuminate\Queue\Events\Looping',
            'BoldMinded\Queue\Queue\Listeners\LoopingListener@handle'
        );

        return $events;
    }
}
