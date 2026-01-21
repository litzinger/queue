<?php

namespace BoldMinded\Queue\Service;

use BoldMinded\Queue\Contracts\QueueServiceProvider;

/**
 * Queue Service
 *
 * Provides Queue addon services to other addons without type conflicts.
 * This allows addons with differently-scoped Laravel Queue dependencies
 * to share a single Queue implementation.
 *
 * @package   Queue
 * @author    BoldMinded, LLC
 */
class QueueService implements QueueServiceProvider
{
    /**
     * Create a new QueueService instance
     *
     * @param mixed $queueManager QueueManager instance
     * @param mixed $queueWorker Worker instance
     * @param mixed $queueWorkerOptions WorkerOptions instance
     */
    public function __construct(
        private $queueManager,
        private $queueWorker,
        private $queueWorkerOptions
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getQueueManager()
    {
        return $this->queueManager;
    }

    /**
     * @inheritDoc
     */
    public function getWorker()
    {
        return $this->queueWorker;
    }

    /**
     * @inheritDoc
     */
    public function getWorkerOptions()
    {
        return $this->queueWorkerOptions;
    }
}
