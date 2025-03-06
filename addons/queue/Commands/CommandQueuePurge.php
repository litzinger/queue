<?php

namespace BoldMinded\Queue\Commands;

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

    ];

    /**
     * Run the command
     * @return mixed
     */
    public function handle()
    {
        $this->info('Hello World!');
    }
}
