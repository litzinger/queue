<?php

namespace BoldMinded\Queue\Queue\Connectors;

use BoldMinded\Queue\Dependency\Illuminate\Queue\SqsQueue;
use BoldMinded\Logit\Dependency\Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

class SqsFifoQueue extends SqsQueue
{
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        try {
            $response = $this->sqs->sendMessage([
                'QueueUrl' => $this->getQueue($queue),
                'MessageBody' => $payload,
                'MessageGroupId' => uniqid(),
                'MessageDeduplicationId' => uniqid(),
            ]);

            return $response->get('MessageId');
        } catch (\Exception $exception) {
            ee()->logger->developer($exception->getMessage());
            return null;
        }
    }
}
