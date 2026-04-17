<?php

namespace App\Console\Commands;

use App\Services\Order\OrderEventHandler;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class ConsumeOrderQueue extends Command
{
    protected $signature = 'rabbitmq:consume-orders';
    protected $description = 'Consume order queue from RabbitMQ';

    public function handle(OrderEventHandler $handler)
    {
        $connection = new AMQPSocketConnection(
            config('services.rabbitmq.host'),
            config('services.rabbitmq.port'),
            config('services.rabbitmq.user'),
            config('services.rabbitmq.password')
        );

        $channel = $connection->channel();

        // Ensure queue exists
        $channel->queue_declare(
            'order_queue',
            false,
            true,
            false,
            false
        );

        //Fair dispatch (important for scaling)
        $channel->basic_qos(null, 1, null);

        $this->info("Order consumer started...");

        $callback = function (AMQPMessage $msg) use ($handler) {

            try {
                // 🔐 Safe decode
                $payload = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);

                logger()->info('Order event received', $payload);

                //Delegate business logic
                $handler->handle($payload);

                //ACK only after success
                $msg->ack();

            } catch (Throwable $e) {

                logger()->error('Order consumer failed', [
                    'error' => $e->getMessage(),
                    'body'  => $msg->body
                ]);

                $msg->nack(false, false);
            }
        };

        $channel->basic_consume(
            'order_queue',
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
