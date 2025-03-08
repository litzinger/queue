<?php

namespace BoldMinded\Queue\Commands;

use BoldMinded\Queue\Queue\Jobs\TestJob;
use ExpressionEngine\Cli\Cli;
use ExpressionEngine\Cli\Exception;

class CommandQueueTestLarge extends Cli
{
    /**
     * name of command
     * @var string
     */
    public $name = 'QueueTestLarge';

    /**
     * signature of command
     * @var string
     */
    public $signature = 'queue:test-large';

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
    public $usage = 'php eecli.php queue:test-large';

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
            $this->info('Adding 5000 jobs to the queue...');
            while ($i < 5000) {
                ee('queue:QueueManager')->push(TestJob::class, rand(0, PHP_INT_MAX));
                $i++;
            }

            $this->info('Checking queue size...');

            $queueStatus = ee('queue:QueueStatus');
            $this->info(sprintf('%d jobs found in the queue', $queueStatus->getSize()));

            $this->info('Running consumer...');

            $queueWorkerOptions = ee('queue:QueueWorkerOptions');
            $queueWorkerOptions->maxJobs = 5;

            $queueWorker = ee('queue:QueueWorker');
            $queueWorker->daemon('default', 'default', $queueWorkerOptions);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
