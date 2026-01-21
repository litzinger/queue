<?php

namespace BoldMinded\Queue\Contracts;

/**
 * Queue Service Provider Interface
 *
 * This interface allows other addons to consume Queue's services without
 * having type conflicts between differently-scoped Laravel Queue dependencies.
 *
 * @package   Queue
 * @author    BoldMinded, LLC
 */
interface QueueServiceProvider
{
    /**
     * Get the Queue Manager instance
     *
     * @return mixed QueueManager instance (scoped to Queue's namespace)
     */
    public function getQueueManager();

    /**
     * Get the Queue Worker instance
     *
     * @return mixed Worker instance (scoped to Queue's namespace)
     */
    public function getWorker();

    /**
     * Get the Queue Worker Options instance
     *
     * @return mixed WorkerOptions instance (scoped to Queue's namespace)
     */
    public function getWorkerOptions();
}
