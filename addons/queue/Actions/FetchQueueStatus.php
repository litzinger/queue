<?php

namespace BoldMinded\Queue\Actions;

use BoldMinded\DataGrab\Queue\Drivers\QueueDriverInterface;

class FetchQueueStatus extends Action
{
    public function process()
    {
        /** @var QueueDriverInterface $queueManager */
        $queueDriver = ee('queue:QueueDriver');

        $pendingQueues = [];
        $failedQueues = [];

        foreach ($queueDriver->getAllPendingQueues() as $queue) {
            $pendingJobs = $queueDriver->getPendingJobs($queue);

            $pendingQueues[] = [
                'queueName' => $queue,
                'count' => count($pendingJobs),
                'jobs' => $this->paginate($pendingJobs),
            ];
        }

        if (empty($pendingQueues)) {
            $pendingQueues = [[
                'queueName' => 'default',
                'count' => 0,
                'jobs' => [],
            ]];
        }

        foreach ($queueDriver->getAllFailedQueues() as $queue) {
            $failedJobs = $queueDriver->getFailedJobs($queue);

            $failedQueues[] = [
                'queueName' => $queue,
                'count' => count($failedJobs),
                'jobs' => $this->paginate($failedJobs),
            ];
        }

        if (empty($failedQueues)) {
            $failedQueues = [[
                'queueName' => 'default',
                'count' => 0,
                'jobs' => [],
            ]];
        }

        $this->sendJsonResponse([
            'pending' => $pendingQueues,
            'failed' => $failedQueues,
        ]);
    }

    private function paginate(array $jobs = []): array
    {
        return array_slice($jobs, 0, 100);
    }
}
