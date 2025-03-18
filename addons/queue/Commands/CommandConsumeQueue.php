<?php

namespace BoldMinded\Queue\Commands;

use ExpressionEngine\Cli\Cli;
use ExpressionEngine\Cli\Exception;
use BoldMinded\Queue\Dependency\Illuminate\Queue\Events;
use BoldMinded\Queue\Dependency\Illuminate\Contracts\Queue\Job;

class CommandConsumeQueue extends Cli
{
    /**
     * name of command
     * @var string
     */
    public $name = 'ConsumeQueue';

    /**
     * signature of command
     * @var string
     */
    public $signature = 'queue:consume';

    /**
     * Public description of command
     * @var string
     */
    public $description = 'Consume jobs in the queue';

    /**
     * Summary of command functionality
     * @var [type]
     */
    public $summary = 'Consume jobs in the queue';

    /**
     * How to use command
     * @var string
     */
    public $usage = 'php eecli.php queue:consume';

    /**
     * options available for use in command
     * @var array
     */
    public $commandOptions = [
        'queue_name,name:' => 'Name of the queue to consume',
        'limit,limit:' => 'Limit number of jobs to consume each time this is executed. Default is 1.',
    ];

    /**
     * Indicates if the worker's event listeners have been registered.
     *
     * @var bool
     */
    private static $hasRegisteredListeners = \false;

    private $container;

    /**
     * Run the command
     * @return mixed
     */
    public function handle()
    {
        $this->container = ee('queue:QueueManager')->getContainer();

        $this->listenForEvents();

        try {
            $queueName = $this->option('--queue_name') ?? 'default';
            $limit = $this->option('--limit') ?? 1;

            $this->info(sprintf('Running %s consumer...', $queueName));

            $queueWorkerOptions = ee('queue:QueueWorkerOptions');
            $queueWorkerOptions->maxJobs = $limit;

            $queueWorker = ee('queue:QueueWorker');
            $queueWorker->daemon('default', $queueName, $queueWorkerOptions);

        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    protected function listenForEvents()
    {
        if (static::$hasRegisteredListeners) {
            return;
        }
        $this->container['events']->listen(Events\JobProcessing::class, function ($event) {
            $this->writeOutput($event->job, 'starting');
        });
        $this->container['events']->listen(Events\JobProcessed::class, function ($event) {
            $this->writeOutput($event->job, 'success');
        });
        $this->container['events']->listen(Events\JobReleasedAfterException::class, function ($event) {
            $this->writeOutput($event->job, 'released_after_exception');
        });
        $this->container['events']->listen(Events\JobFailed::class, function ($event) {
            $this->writeOutput($event->job, 'failed', $event->exception);
        });
        static::$hasRegisteredListeners = \true;
    }

    protected function writeOutput(Job $job, $status)
    {
        $this->info("{$job->resolveName()}<#{$job->getJobId()}> - {$status}");
    }

}
