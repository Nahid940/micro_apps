<?php

namespace App\Services\Event;

use App\Services\RabbitMQService;
use Illuminate\Support\Str;

class EventDispatcher
{
    protected $rabbitMQ;

    public function __construct(
        RabbitMQService $rabbitMQ
    ) {
        $this->rabbitMQ = $rabbitMQ;
    }

    public function dispatch(string $exchange, object $event): void
    {
        $this->rabbitMQ->publish(
            $exchange,
            $event->routingKey(),
            $this->buildEnvelope($event)
        );
    }

    /**
     * Standard event structure (VERY IMPORTANT)
     */
    private function buildEnvelope(object $event): array
    {
        return [
            'event_id'  => (string) Str::uuid(),
            'event'     => $event->routingKey(),
            'data'      => $event->payload(),
            'timestamp' => now()->toISOString(),
            'version'   => '1.0',
        ];
    }
}
