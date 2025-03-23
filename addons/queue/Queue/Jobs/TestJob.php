<?php

namespace BoldMinded\Queue\Queue\Jobs;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job;
use ExpressionEngine\Cli\CliFactory;

class TestJob extends AbstractJob
{
    /*
     * If you typehint job, be sure to use a union. If using Horizon to process
     * jobs it will expect the non-vendored version of the job, and if using Redis
     * it could come in as RedisJob, not as \Illuminate\Contracts\Queue\Job
     * or \BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job
     */
    public function fire(
        Job|\Illuminate\Contracts\Queue\Job $job,
        string|array $payload): bool
    {
        $factory = new CliFactory();
        $output = $factory->newStdio();

        $output->outln('<<yellow>>Processed:<<reset>>');

        if (is_array($payload)) {
            $display = '<<dim>>'. json_encode($payload, JSON_UNESCAPED_UNICODE) .'<<reset>>';
        } else {
            $display = '<<dim>>'. $payload .'<<reset>>';
        }

        $output->outln($display);

        $job->delete();

        return true;
    }
}
