<?php

use ExpressionEngine\Service\Migration\Migration;

class Createactionpurgequeueforaddonqueue extends Migration
{
    /**
     * Execute the migration
     * @return void
     */
    public function up()
    {
        ee('Model')->make('Action', [
            'class' => 'Queue',
            'method' => 'PurgeQueue',
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
            ->filter('method', 'PurgeQueue')
            ->delete();
    }
}
