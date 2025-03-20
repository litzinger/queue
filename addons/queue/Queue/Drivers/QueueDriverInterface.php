<?php

namespace BoldMinded\Queue\Queue\Drivers;

use ExpressionEngine\Core\Provider;

interface QueueDriverInterface
{
    public function __construct(
        Provider $provider,
        array $config = [],
    );

    public function getQueueManager();

    public function getPendingJobs(string $queueName): array;

    public function getFailedJobs(string $queueName): array;

    public function getAllPendingQueues(): array;

    public function getAllFailedQueues(): array;

    public function getFailedJobByUUID(string $jobId): array|null;

    public function deleteFailedJobByUUID(string $jobId): bool;
}
