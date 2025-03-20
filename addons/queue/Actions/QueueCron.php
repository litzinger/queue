<?php

namespace BoldMinded\Queue\Actions;

class QueueCron extends Action
{
    public function process()
    {
        try {
            $queueName = ee()->input->get('queue_name') ?? 'default';
            $limit = ee()->input->get('limit') ?? 100;

            $queueWorkerOptions = ee('queue:QueueWorkerOptions');
            $queueWorkerOptions->maxJobs = $limit;

            $queueWorker = ee('queue:QueueWorker');
            $queueWorker->daemon('default', $queueName, $queueWorkerOptions);

            $queueStatus = ee('queue:QueueStatus');

            $this->sendJsonResponse([
                'success' => true,
                'queue' => $queueName,
                'size' => $queueStatus->getSize(),
            ]);
        } catch (\Exception $e) {
            $this->sendJsonResponse([
                'success' => false,
                'queue' => $queueName,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
