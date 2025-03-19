<?php

namespace BoldMinded\Queue\Commands;

use BoldMinded\Queue\Queue\Jobs\TestJob;
use ExpressionEngine\Cli\Cli;
use ExpressionEngine\Cli\Exception;

class CommandQueueTest extends Cli
{
    /**
     * name of command
     * @var string
     */
    public $name = 'QueueTest';

    /**
     * signature of command
     * @var string
     */
    public $signature = 'queue:test';

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
    public $usage = 'php eecli.php queue:test';

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
            $this->info('Adding 5 jobs to the queue...');

            ee('queue:QueueManager')->push(TestJob::class, 'Job #1, string payload');
            ee('queue:QueueManager')->push(TestJob::class, 'Job #2, string payload');
            ee('queue:QueueManager')->push(TestJob::class, ['Job #3, array payload']);
            ee('queue:QueueManager')->push(TestJob::class, ['Job #4, array payload']);
            ee('queue:QueueManager')->push(TestJob::class, ['Job #5, array payload']);

            $this->info('Checking queue size...');

            $queueStatus = ee('queue:QueueStatus');
            $this->info(sprintf('%d jobs found in the queue', $queueStatus->getSize()));
//
//            $this->info('Running consumer...');
//
//            $queueWorkerOptions = ee('queue:QueueWorkerOptions');
//            $queueWorkerOptions->maxJobs = 50;
//
//            $queueWorker = ee('queue:QueueWorker');
//            $queueWorker->daemon('default', 'default', $queueWorkerOptions);
//
//            $this->info(sprintf('%d jobs found in the queue', $queueStatus->getSize()));
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
