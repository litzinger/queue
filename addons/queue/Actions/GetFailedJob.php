<?php

namespace BoldMinded\Queue\Actions;

use BoldMinded\Queue\Queue\Drivers\QueueDriverInterface;

class GetFailedJob extends Action
{
    public function process()
    {
        $jobId = ee()->input->get('jobId') ?? '';

        if (!$jobId) {
            return false;
        }

        /** @var QueueDriverInterface $queueDriver */
        $queueDriver = ee('queue:QueueDriver');

        $result = $queueDriver->getFailedJobByUUID($jobId);
        $result['payload'] = json_decode($result['payload'] ?? '', true);

        $this->sendJsonResponse($result);

        return true;
    }
}
