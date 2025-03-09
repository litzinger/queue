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
            $pendingJobs = $queueDriver->getPendingJobs($queue);
            $failedJobs = $queueDriver->getFailedJobs($queue);

            $queues[] = [
                'queueName' => $queue,
                'pendingCount' => count($pendingJobs),
                'pending' => $this->paginate($pendingJobs),
                'failedCount' => count($failedJobs),
                'failed' => $this->paginate($failedJobs),
            ];
        }

        $this->sendJsonResponse($queues);
    }

    private function paginate(array $jobs = []): array
    {
        return array_slice($jobs, 0, 100);
    }
}
