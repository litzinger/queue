<?php

namespace BoldMinded\Queue\Queue\Drivers;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Capsule\Manager as QueueCapsuleManager;
use BoldMinded\Queue\Dependency\Illuminate\Database\ConnectionResolver;
use BoldMinded\Queue\Dependency\Illuminate\Database\DatabaseManager;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Connectors\DatabaseConnector;
use BoldMinded\Queue\Dependency\Illuminate\Queue\QueueManager;
use BoldMinded\Queue\Dependency\Illuminate\Support\Collection;
use ExpressionEngine\Core\Provider;

class DatabaseDriver implements QueueDriverInterface
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param Provider $provider
     */
    public function __construct(Provider $provider, array $config = [])
    {
        $this->config = $config;
        $this->provider = $provider;
    }

    /**
     * @return QueueManager
     */
    public function getQueueManager(): QueueManager|\Illuminate\Queue\QueueManager
    {
        $capsuleQueueManager = new QueueCapsuleManager;

        /** @var DatabaseManager $database */
        $database = $this->provider->make('DatabaseManager');

        $capsuleQueueManager->addConnector('database', function () use ($database) {
            $connection = $database->getConnection();
            $connectionResolver = new ConnectionResolver(['default' => $connection]);
            $connectionResolver->setDefaultConnection('default');

            return new DatabaseConnector($connectionResolver);
        });

        $capsuleQueueManager->addConnection([
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 60 * 5,
            'after_commit' => false,
        ]);

        $this->getPendingJobs();

        return $capsuleQueueManager->getQueueManager();
    }

    public function getPendingJobs()
    {
        $database = ee('queue:DatabaseManager');

        /** @var Collection $jobs */
        $jobs = $database->getConnection()->table('jobs')->get();

        return [];
    }

    public function getFailedJobs()
    {
        $database = ee('queue:DatabaseManager');

        /** @var Collection $jobs */
        $jobs = $database->getConnection()->table('failed_jobs')->get();

        return [];
    }
}
