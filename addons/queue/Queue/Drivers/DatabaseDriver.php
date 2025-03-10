<?php

namespace BoldMinded\Queue\Queue\Drivers;

use BoldMinded\Queue\Dependency\Illuminate\Container\Container;
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

        $capsuleQueueManager->getContainer()['config']['queue.failed.driver'] = 'database';
        $capsuleQueueManager->getContainer()['config']['queue.failed.database'] = 'default';
        $capsuleQueueManager->getContainer()['config']['queue.failed.table'] = 'failed_jobs';

        $capsuleQueueManager->getContainer()['db'] = $database;

//        $capsuleQueueManager->getContainer()->instance('database', $database);
//
//        $container->singleton('queue.failer', function () use ($capsuleQueueManager) {
//            return new DatabaseFailedJobProvider(
//                $capsuleQueueManager->getConnection(),
//                'failed_jobs'
//            );
//        });

        $capsuleQueueManager->setAsGlobal();

//        $capsuleQueueManager->getQueueManager()->setFailer($container->make('queue.failer'));

        return $capsuleQueueManager->getQueueManager();
    }

    public function getPendingJobs(string $queueName = 'default'): array
    {
        $database = ee('queue:DatabaseManager');

        /** @var Collection $jobs */
        $jobs = $database->getConnection()
            ->table('jobs')
            ->where('queue', $queueName)
            ->get();

        return array_map(function ($job) {
            return [
                'id' => $job->id,
                'queue' => $job->queue,
                'payload' => $job->payload,
                'attempts' => $job->attempts,
                'available_at' => $job->available_at,
                'created_at' => $job->created_at,
                'reserved_at' => $job->reserved_at,
            ];
        }, $jobs->toArray());
    }

    public function getFailedJobs(string $queueName = 'default'): array
    {
        $database = ee('queue:DatabaseManager');

        /** @var Collection $jobs */
        $jobs = $database->getConnection()->table('failed_jobs')->get();

        return array_map(function ($job) {
            return [
                'id' => $job->id,
                'queue' => $job->queue,
                'payload' => $job->payload,
                'exception' => $job->exception,
                'failed_at' => $job->failed_at,
            ];
        }, $jobs->toArray());
    }

    public function totalFailedJobs(string $queueName = 'default'): int
    {
        $database = ee('queue:DatabaseManager');

        /** @var Collection $jobs */
        $jobs = $database->getConnection()->table('failed_jobs')->get();

        return count($jobs->toArray());
    }

    public function getAllQueues(): array
    {
        $database = ee('queue:DatabaseManager');

        return $database->getConnection()->table('jobs')
            ->distinct()
            ->pluck('queue')
            ->toArray();
    }
}
