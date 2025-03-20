<?php

namespace BoldMinded\Queue\Queue\Drivers;

use BoldMinded\Queue\Dependency\Illuminate\Database\Capsule\Manager as DatabaseCapsuleManager;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Capsule\Manager as QueueCapsuleManager;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Connectors\RedisConnector;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Failed\DatabaseUuidFailedJobProvider;
use BoldMinded\Queue\Dependency\Illuminate\Queue\QueueManager;
use BoldMinded\Queue\Dependency\Illuminate\Redis\Connections\PhpRedisConnection;
use BoldMinded\Queue\Dependency\Illuminate\Redis\RedisManager;
use BoldMinded\Queue\Dependency\Illuminate\Support\Facades\App;
use ExpressionEngine\Core\Provider;

class RedisDriver implements QueueDriverInterface
{
    private array $config = [];

    private Provider $provider;

    /**
     * @param Provider $provider
     */
    public function __construct(
        Provider $provider,
        array $config = [],
    )
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
        $container = $capsuleQueueManager->getContainer();

        $capsuleQueueManager->addConnector('redis', function () {
            // Could be predis too, but might need additional dependencies
            $redisManager = new RedisManager(new App(), 'phpredis', $this->config);
            return new RedisConnector($redisManager);
        });

        $capsuleQueueManager->addConnection([
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 60 * 5,
            'block_for' => 5,
        ]);

        /** @var DatabaseDriver $database */
        $database = $this->provider->make('DatabaseDriver');
        /** @var DatabaseCapsuleManager $databaseManager */
        $databaseManager = $this->provider->make('DatabaseManager');

        $container['config']['queue.failed.driver'] = 'database';
        $container['config']['queue.failed.database'] = 'default';
        $container['config']['queue.failed.table'] = 'failed_jobs';
        $container['db'] = $database;

        $container['queue.failer'] = new DatabaseUuidFailedJobProvider(
            $database->getConnectionResolver($databaseManager),
            'default',
            'failed_jobs'
        );

        return $capsuleQueueManager->getQueueManager();
    }

    public function getPendingJobs(string $queueName = 'default'): array
    {
        return $this->getJobsFromQueue($queueName);
    }

    public function getFailedJobs(string $queueName = 'default'): array
    {
        /** @var DatabaseDriver $database */
        $database = $this->provider->make('DatabaseDriver');

        return $database->getFailedJobs($queueName);
    }

    public function getFailedJobByUUID(string $jobId): array|null
    {
        /** @var DatabaseDriver $database */
        $database = $this->provider->make('DatabaseDriver');

        return $database->getFailedJobByUUID($jobId);
    }

    public function deleteFailedJobByUUID(string $jobId): bool
    {
        /** @var DatabaseDriver $database */
        $database = $this->provider->make('DatabaseDriver');

        return $database->deleteFailedJobByUUID($jobId);
    }

    public function getAllPendingQueues(): array
    {
        // Get all the queue names, but we're not interested in the "notify" queues
        return array_filter(array_map(
            fn($key) => str_replace('queues:', '', $key),
            $this->getConnection()->keys('queues:*')
        ), fn($key) => !str_contains($key ?? '', 'notify'));
    }

    public function getAllFailedQueues(): array
    {
        /** @var DatabaseDriver $database */
        $database = $this->provider->make('DatabaseDriver');

        return $database->getAllFailedQueues();
    }

    private function getJobsFromQueue(string $queueName): array
    {
        return array_map(function ($job) use ($queueName) {
            $decoded = json_decode($job);

            return [
                'id' => $decoded->uuid,
                'queue' => $queueName,
                'payload' => $decoded->data,
                'attempts' => $decoded->attempts,
                'available_at' => $decoded->available_at ?? 0,
                'created_at' => $decoded->created_at ?? 0,
                'reserved_at' => $decoded->reserved_at ?? 0,
            ];
        }, $this->getConnection()->lRange(sprintf('queues:%s', $queueName), 0, -1));
    }

    private function getConnection(): PhpRedisConnection
    {
        return $this->getQueueManager()->getConnection('default');
    }
}
