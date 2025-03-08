<?php

namespace BoldMinded\Queue\Queue\Drivers;

use BoldMinded\Queue\Dependency\Illuminate\Queue\Capsule\Manager as QueueCapsuleManager;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Connectors\RedisConnector;
use BoldMinded\Queue\Dependency\Illuminate\Queue\QueueManager;
use BoldMinded\Queue\Dependency\Illuminate\Redis\RedisManager;
use BoldMinded\Queue\Dependency\Illuminate\Support\Facades\App;
use BoldMinded\Queue\Dependency\Illuminate\Support\Facades\Redis;
use ExpressionEngine\Core\Provider;

class RedisDriver implements QueueDriverInterface
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

        return $capsuleQueueManager->getQueueManager();
    }

    public function getPendingJobs(string $queueName = 'default'): array
    {
        // @todo
        return [];
    }

    public function getFailedJobs(string $queueName = 'default'): array
    {
        // @todo
        return [];
    }

    public function totalFailedJobs(string $queueName = 'default'): int
    {
        // @todo
        return 0;
    }

    public function getAllQueues(): array
    {
        return array_map(fn($key) => str_replace('queues:', '', $key), Redis::keys('queues:*'));
    }
}
