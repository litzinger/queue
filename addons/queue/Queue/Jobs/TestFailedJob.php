<?php

namespace BoldMinded\Queue\Queue\Jobs;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldBeUnique;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldQueue;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Events\JobExceptionOccurred;
use Exception;

class TestFailedJob extends AbstractJob implements ShouldQueue, ShouldBeUnique
{
    public function fire(Job $job, string|array $payload): bool
    {
        throw new Exception('Intentional failure for testing.');
    }

    public function failed($payloadData, $exception, $uuid): bool
    {
        return true;
    }
}
