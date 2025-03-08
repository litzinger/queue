<?php

namespace BoldMinded\Queue\Actions;

use BoldMinded\DataGrab\Queue\Drivers\QueueDriverInterface;
use BoldMinded\Queue\Service\QueueStatus;

class FetchQueueStatus extends Action
{
    public function process()
    {
        /** @var QueueStatus $queueStatus */
        $queueStatus = ee('queue:QueueStatus');
        /** @var QueueDriverInterface $queueManager */
        $queueDriver = ee('queue:QueueDriver');

        $queues = [];

        foreach ($queueDriver->getAllQueues() as $queue) {
            $queues[$queue] = [
                'pending' => $this->paginate($queueDriver->getPendingJobs($queue)),
                'failed' => $this->paginate($queueDriver->getFailedJobs($queue)),
            ];
        }

        $this->sendJsonResponse([
            'size' => $queueStatus->getSize(),
            'pending' => array_slice($queueDriver->getPendingJobs(), 0, 100),
            'failed' => array_slice($queueDriver->getFailedJobs(), 0, 100),
        ]);
    }

    private function paginate(array $jobs = []): array
    {
        return array_slice($jobs, 0, 100);
    }
}
