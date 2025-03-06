<?php

namespace BoldMinded\Queue\Queue\Jobs;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job;

class AbstractJob
{
    protected Job $job;

    protected array $settings = [];

    protected static $cache = [];

    /**
     * @return bool
     */
    protected function isCli(): bool
    {
        return defined('STDIN') && php_sapi_name() === 'cli';
    }
}
