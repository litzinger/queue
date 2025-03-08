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

    public function getPendingJobs(): array
    {
        // @todo
        return [];

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

    public function getFailedJobs(): array
    {
        // @todo
        return [];

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

    public function totalFailedJobs(): int
    {
        // @todo
        return 0;
    }
}
