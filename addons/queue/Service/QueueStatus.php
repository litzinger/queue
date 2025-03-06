<?php

namespace BoldMinded\Queue\Service;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Queue;

class QueueStatus
{
    /**
     * @param string $importId
     * @return array
     */
    public function fetch(string $importId = ''): array
    {
        return [];
    }

    /**
     * @param string $queueName
     * @return int
     */
    public function clear(string $queueName): int
    {
        return $this->getQueueConnection()->clear($queueName);
    }

    /**
     * @return \Illuminate\Contracts\Queue\Queue
     */
    private function getQueueConnection(): Queue
    {
        /** @var \BoldMinded\Queue\Dependency\Illuminate\Queue\QueueManager $queue */
        $queue = ee('queue:QueueManager');

        return $queue->connection('default');
    }
}
