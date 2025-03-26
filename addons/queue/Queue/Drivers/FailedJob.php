<?php

namespace BoldMinded\Queue\Queue\Drivers;

final class FailedJob
{
    public function __construct(
        public int $id = 0,
        public string $uuid = '',
        public string $queue = 'default',
        public string $payload = '',
        public string $exception = '',
        public string $failed_at = '',
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'queue' => $this->queue,
            'payload' => $this->payload,
            'exception' => $this->exception,
            'failed_at' => $this->failed_at,
        ];
    }
}
