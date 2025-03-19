<?php

namespace BoldMinded\Queue\Actions;

use BoldMinded\Queue\Service\QueueStatus;

class PurgeAllPendingJobs extends Action
{
    public function process()
    {
        $queueName = ee()->input->post('queueName') ?? '';

        if (!$queueName) {
            return false;
        }

        /** @var QueueStatus $queueStatus */
        $queueStatus = ee('queue:QueueStatus');
        $queueStatus->clear($queueName);

        $this->sendJsonResponse([
            'success' => true,
        ]);

        return true;
    }
}
