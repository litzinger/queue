<?php

use BoldMinded\Queue\Dependency\Illuminate\Database\Schema\Blueprint;
use ExpressionEngine\Service\Migration\Migration;

class Install extends Migration
{
    private array $actions = [
        'fetchQueueStatus',
        'purgeAllPendingJobs',
        'retryFailedJob',
        'queueCron',
        'deleteFailedJob',
        'getFailedJob',
    ];

    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        // Using Laravel here to create the tables
        $database = ee('queue:DatabaseManager');
        $schema = $database->getConnection()->getSchemaBuilder();

        if (!ee('db')->table_exists('queue_failed_jobs')) {
            $schema->create('failed_jobs', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        if (!ee('db')->table_exists('queue_jobs')) {
            $schema->create('jobs', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        foreach ($this->actions as $action) {
            ee('Model')->make('Action', [
                'class' => 'Queue',
                'method' => $action,
                'csrf_exempt' => false,
            ])->save();
        }
    }

    /**
     * Rollback the migration
     * @return void
     */
    public function down()
    {
        ee('Model')->get('Action')
            ->filter('class', 'Queue')
            ->filter('method', 'IN', $this->actions)
            ->delete();
    }
}
