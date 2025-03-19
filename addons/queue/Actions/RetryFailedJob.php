<?php

namespace BoldMinded\Queue\Actions;

use BoldMinded\Queue\Dependency\Illuminate\Queue\QueueManager;

class RetryFailedJob extends Action
{
    public function process()
    {
        $jobId = ee()->input->post('jobId') ?? '';

        if (!$jobId) {
            return false;
        }

        $queueDriver = ee('queue:QueueDriver');

        $job = $queueDriver->getFailedJobByUUID($jobId);

        $payload = json_decode($job['payload'], true);

        $jobClass = $payload['job'];

        /** @var QueueManager $queueManger */
        $queueManger = ee('queue:QueueManager');
        $queueManger->push($jobClass, $payload['data'], $job['queue']);

        $result = $queueDriver->deleteFailedJobByUUID($jobId);

        $this->sendJsonResponse([
            'success' => boolval($result),
        ]);

        return true;
    }
}
