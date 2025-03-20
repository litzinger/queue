<?php

namespace BoldMinded\Queue\Commands;

use BoldMinded\Queue\Service\QueueStatus;
use ExpressionEngine\Cli\Cli;

class CommandQueuePurge extends Cli
{
    /**
     * name of command
     * @var string
     */
    public $name = 'QueuePurge';

    /**
     * signature of command
     * @var string
     */
    public $signature = 'queue:purge';

    /**
     * Public description of command
     * @var string
     */
    public $description = 'Purge all jobs in a queue';

    /**
     * Summary of command functionality
     * @var [type]
     */
    public $summary = 'Purge all jobs in a queue';

    /**
     * How to use command
     * @var string
     */
    public $usage = 'php eecli.php queue:purge';

    /**
     * options available for use in command
     * @var array
     */
    public $commandOptions = [
        'queue_name,name:' => 'Name of the queue to purge',
    ];

    /**
     * Run the command
     * @return mixed
     */
    public function handle()
    {
        try {
            $queueName = $this->option('--queue_name') ?? 'default';

            if (!$queueName) {
                return false;
            }

            /** @var QueueStatus $queueStatus */
            $queueStatus = ee('queue:QueueStatus');
            $queueStatus->clear($queueName);

            $this->info(sprintf(
                '%s queue purged, %d jobs found in the queue',
                $queueName,
                $queueStatus->getSize()
            ));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
