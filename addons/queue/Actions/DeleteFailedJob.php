<?php

namespace BoldMinded\Queue\Actions;

use BoldMinded\Queue\Queue\Drivers\QueueDriverInterface;

class DeleteFailedJob extends Action
{
    public function process()
    {
        $jobId = ee()->input->post('jobId') ?? '';

        if (!$jobId) {
            return false;
        }

        /** @var QueueDriverInterface $queueDriver */
        $queueDriver = ee('queue:QueueDriver');

        $result = $queueDriver->deleteFailedJobByUUID($jobId);

        $this->sendJsonResponse([
            'success' => boolval($result),
        ]);

        return true;
    }
}
