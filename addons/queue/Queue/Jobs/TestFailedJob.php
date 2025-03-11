<?php

namespace BoldMinded\Queue\Queue\Jobs;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldBeUnique;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldQueue;
use Exception;

class TestFailedJob extends AbstractJob implements ShouldQueue, ShouldBeUnique
{
    public function fire(Job $job, string|array $payload): bool
    {
        throw new Exception('Marking job as failed intentionally.');

        return false;
    }

    public function failed($payloadData, $exception, $uuid): bool
    {
        return true;
    }
}
