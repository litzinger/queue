<?php

namespace BoldMinded\Queue\Actions;

class FetchQueueStatus extends Action
{
    public function process()
    {
        $queueStatus = ee('queue:QueueStatus');

        $this->sendJsonResponse([
            'size' => $queueStatus->getSize()
        ]);
    }
}
