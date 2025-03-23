<?php

namespace BoldMinded\Queue\Queue\Jobs;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldBeUnique;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\ShouldQueue;

class AbstractJob implements ShouldQueue, ShouldBeUnique
{
    protected $job;

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
