<?php

namespace BoldMinded\Queue\Commands;

use ExpressionEngine\Cli\Cli;
use ExpressionEngine\Cli\Exception;

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
     * Run the command
     * @return mixed
     */
    public function handle()
    {
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
}
