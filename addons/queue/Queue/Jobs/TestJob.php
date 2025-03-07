<?php

namespace BoldMinded\Queue\Queue\Jobs;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldBeUnique;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldQueue;
use ExpressionEngine\Cli\CliFactory;

class TestJob extends AbstractJob implements ShouldQueue, ShouldBeUnique
{
    public function fire(Job $job, string|array $payload): bool
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
