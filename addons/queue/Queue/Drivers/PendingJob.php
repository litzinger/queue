<?php

namespace BoldMinded\Queue\Queue\Drivers;

final class PendingJob
{
    public function __construct(
        public string $id,
        public string $queue = 'default',
        public string $payload = '',
        public int $attempts = 0,
        public int $available_at = 0,
        public int $created_at = 0,
        public int $reserved_at = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'queue' => $this->queue,
            'payload' => $this->payload,
            'attempts' => $this->attempts,
            'available_at' => $this->available_at,
            'created_at' => $this->created_at,
            'reserved_at' => $this->reserved_at,
        ];
    }
}
