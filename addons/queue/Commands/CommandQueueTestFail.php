<?php

namespace BoldMinded\Queue\Commands;

use BoldMinded\Queue\Queue\Jobs\TestFailedJob;
use ExpressionEngine\Cli\Cli;
use ExpressionEngine\Cli\Exception;

class CommandQueueTestFail extends Cli
{
    /**
     * name of command
     * @var string
     */
    public $name = 'QueueTestFail';

    /**
     * signature of command
     * @var string
     */
    public $signature = 'queue:test-fail';

    /**
     * Public description of command
     * @var string
     */
    public $description = 'Simple command to test queue operations';

    /**
     * Summary of command functionality
     * @var [type]
     */
    public $summary = 'Simple command to test queue operations';

    /**
     * How to use command
     * @var string
     */
    public $usage = 'php eecli.php queue:test-faile';

    /**
     * options available for use in command
     * @var array
     */
    public $commandOptions = [

    ];

    /**
     * Run the command
     * @return mixed
     */
    public function handle()
    {
        try {
            $i = 1;
            $this->info('Adding 1 job that will fail to the queue...');

            ee('queue:QueueManager')->push(TestFailedJob::class, rand(0, PHP_INT_MAX));
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
