<?php

namespace BoldMinded\Queue\Actions;

use BoldMinded\DataGrab\Queue\Drivers\QueueDriverInterface;

class FetchQueueStatus extends Action
{
    public function process()
    {
        $queueStatus = ee('queue:QueueStatus');
        /** @var QueueDriverInterface $queueManager */
        $queueDriver = ee('queue:QueueDriver');

        $this->sendJsonResponse([
            'size' => $queueStatus->getSize(),
            // @todo provide a way to paginate?
            'pending' => array_slice($queueDriver->getPendingJobs(), 0, 100),
            'failed' => array_slice($queueDriver->getFailedJobs(), 0, 100),
        ]);
    }
}
