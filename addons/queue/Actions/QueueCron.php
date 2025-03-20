<?php

namespace BoldMinded\Queue\Actions;

class QueueCron extends Action
{
    public function process()
    {
        $queueName = ee()->input->get('queue_name') ?? 'default';
        $limit = ee()->input->get('limit') ?? 100;

        $queueWorkerOptions = ee('queue:QueueWorkerOptions');
        $queueWorkerOptions->maxJobs = $limit;

        $queueWorker = ee('queue:QueueWorker');
        $queueWorker->daemon('default', $queueName, $queueWorkerOptions);
    }
}
