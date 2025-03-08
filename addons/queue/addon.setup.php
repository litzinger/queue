<?php

// Build: {QUEUE_BUILD_VERSION}
require_once PATH_THIRD . 'queue/vendor-build/autoload.php';

use BoldMinded\Queue\Dependency\Illuminate\Events\Dispatcher;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Worker;
use BoldMinded\Queue\Dependency\Illuminate\Queue\WorkerOptions;
use BoldMinded\Queue\Dependency\Illuminate\Database\Capsule\Manager as DatabaseCapsuleManager;
use BoldMinded\Queue\Dependency\Illuminate\Queue\QueueManager;
use BoldMinded\Queue\Dependency\Litzinger\Basee\Logger;
use BoldMinded\Queue\Dependency\Litzinger\Basee\Setting;
use BoldMinded\Queue\Queue\Drivers\DatabaseDriver;
use BoldMinded\Queue\Queue\Drivers\RedisDriver;
use BoldMinded\Queue\Queue\Drivers\SQSDriver;
use BoldMinded\Queue\Queue\Exceptions\QueueException;
use BoldMinded\Queue\Queue\Subscribers\QueueSubscriber;
use BoldMinded\Queue\Service\QueueStatus;

if (!defined('QUEUE_NAME')) {
    define('QUEUE_VERSION', '@VERSION@');
    define('QUEUE_BUILD_VERSION', '@BUILD_VERSION@');

    if (defined('BASE')) {
        define('QUEUE_URL', BASE . AMP . '?/cp/addons/settings/queue');
        define('QUEUE_PATH', '?/cp/addons/settings/queue');
    }

    define('QUEUE_NAME', 'Queue');
}

return [
    'name'  => QUEUE_NAME,
    'description' => 'ExpressionEngine\'s missing queue module',
    'version' => QUEUE_VERSION,
    'namespace' => 'BoldMinded\Queue',
    'settings_exist' => true,

    'requires' => [
        'php' => '8.2',
        'ee' => '7.5'
    ],

    'services.singletons' => [
        'Setting' => function () {
            return new Setting('queue_settings');
        },
        'DatabaseConfig' => function () {
            return [
                'driver' => 'mysql',
                'host' => ee('db')->hostname,
                'database' => ee('db')->database,
                'port' => ee('db')->port,
                'username' => ee('db')->username,
                'password' => ee('db')->password,
                'charset' => ee('db')->char_set,
                'collation' => ee('db')->dbcollat,
                'prefix' => ee('db')->dbprefix . 'queue_',
            ];
        },
        'DatabaseManager' => function ($provider) {
            $databaseManager = new DatabaseCapsuleManager;
            $databaseManager->addConnection($provider->make('DatabaseConfig'));

            return $databaseManager;
        },
        'QueueDriver' => function ($provider) {
            $config = ee()->config->item('queue') ?: [];
            $driver = $config['driver'] ?? 'database';

            // @todo this is incomplete and unsupported
            if ($driver === 'sqs') {
                return new SQSDriver($provider, $config['sqs_config'] ?? []);
            }

            if ($driver === 'redis') {
                return new RedisDriver($provider, ['default' => $config['redis_config'] ?? []]);
            }

            return new DatabaseDriver($provider);
        },
        'QueueManager' => function ($provider) {
            $queueDriver = $provider->make('QueueDriver');
            return $queueDriver->getQueueManager();
        },
        'QueueWorker' => function ($provider) {
            /** @var QueueManager $queueManager */
            $queueManager = $provider->make('QueueManager');

            $dispatcher = new Dispatcher($queueManager->getContainer());
            $dispatcher->subscribe(new QueueSubscriber);

            return new Worker(
                $queueManager,
                $dispatcher,
                new QueueException,
                function () {
                },
                function () {
                }
            );
        },
        'QueueWorkerOptions' => function () {
            // Determine how long a worker can run without timing out based on the PHP settings.
            $maxExecutionTime = (ini_get('max_execution_time') ?: 130) - 10;

            // Set minimum timeout, especially if PHP's CLI config does not have max_execution_time set.
            if ($maxExecutionTime < 30) {
                $maxExecutionTime = 30;
            }

            $config = ee()->config->item('queue') ?: [];

            return new WorkerOptions(
                name: $config['name'] ?? 'default',
                backoff: $config['backoff'] ?? 0,
                memory: $config['memory'] ?? 1024,
                timeout: $config['timeout'] ?? $maxExecutionTime,
                sleep: $config['sleep'] ?? 3,
                maxTries: $config['max_tries'] ?? 3,
                force: $config['force'] ?? false,
                stopWhenEmpty: $config['stop_when_empty'] ?? true,
                maxJobs: $config['max_jobs'] ?? 1,
                maxTime: $config['max_time'] ?? $maxExecutionTime,
                rest: $config['rest'] ?? 0
            );
        },
        'QueueStatus' => function () {
            return new QueueStatus();
        },
        'Logger' => function () {
            ee()->load->library('logger');
            $config = ee()->config->item('queue') ?: [];

            return new Logger(
                logger: ee()->logger,
                enabled: get_bool_from_string($config['enable_logging'] ?? 'no'),
            );
        },
    ],
    'commands' => [
        'queue:test' => BoldMinded\Queue\Commands\CommandQueueTest::class,
        'queue:test-large' => BoldMinded\Queue\Commands\CommandQueueTestLarge::class,
        'queue:purge' => BoldMinded\Queue\Commands\CommandQueuePurge::class,
        'queue:consume' => BoldMinded\Queue\Commands\CommandConsumeQueue::class,
    ],
];
