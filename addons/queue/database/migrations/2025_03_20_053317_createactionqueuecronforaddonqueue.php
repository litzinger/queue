<?php

use ExpressionEngine\Service\Migration\Migration;

class Createactionqueuecronforaddonqueue extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Queue',
            'method' => 'queueCron',
            'csrf_exempt' => false,
        ])->save();
    }

    /**
     * Rollback the migration
     * @return void
     */
    public function down()
    {
        ee('Model')->get('Action')
            ->filter('class', 'Queue')
            ->filter('method', 'queueCron')
            ->delete();
    }
}
