<?php

namespace App\Services\Order;

use App\Models\EventLog;

class OrderEventHandler
{
    /**
     * Create a new class instance.
     */
    public function handle(array $event): void
    {
        match ($event['event']) {

            'user.validated' => $this->handleUserValidated($event['data']),

            default => logger()->warning('Unknown event', $event)
        };
    }

    private function handleUserValidated(array $data): void
    {
        //Idempotency check (VERY IMPORTANT)
        $exists = false; //Order::where('user_id', $data['id'])->exists();

        if ($exists) {
            logger()->info('Duplicate event ignored', $data);
            return;
        }

        EventLog::create([
            'event' => 'Received a new event from MQ => '.json_encode($data),
        ]);
    }
}
