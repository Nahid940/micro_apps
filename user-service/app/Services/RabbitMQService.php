<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// AMQPStreamConnection
class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {

        if ($this->connection && $this->channel) {
            return;
        }

        $this->connection = new AMQPStreamConnection(
            config('services.rabbitmq.host'),
            config('services.rabbitmq.port'),
            config('services.rabbitmq.user'),
            config('services.rabbitmq.password'),
            config('services.rabbitmq.vhost', '/')
        );

        $this->channel = $this->connection->channel();

        $this->channel->confirm_select();
    }

    // Reliability (confirm delivery)
    // Observability (logging, tracing)
    // Safety (serialization, validation)
    // Performance (connection reuse)
    // Standards (event structure)

    public function publish(
        string $exchange,
        string $routingKey,
        array $payload,
        array $headers = []
    ): bool {
        try {
            $event = [
                'id'        => (string) Str::uuid(),
                'event'     => $routingKey,
                'data'      => $payload,
                'timestamp' => Carbon::now()->toISOString(),
                'version'   => '1.0',
            ];

            $msg = new AMQPMessage(
                json_encode($event, JSON_THROW_ON_ERROR),
                [
                    'content_type'  => 'application/json',
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                    'message_id'    => $event['id'],
                    'timestamp'     => time(),
                    'application_headers' => new \PhpAmqpLib\Wire\AMQPTable($headers),
                ]
            );

            //Publisher confirm mode (CRITICAL)
            $this->channel->confirm_select();

            $this->channel->basic_publish($msg, $exchange, $routingKey);

            // Wait for broker ACK
            $this->channel->wait_for_pending_acks_returns(5);

            return true;

        } catch (\Throwable $e) {
            logger()->error('RabbitMQ publish failed', [
                'exchange'   => $exchange,
                'routingKey' => $routingKey,
                'payload'    => $payload,
                'error'      => $e->getMessage(),
            ]);

            return false;
        }
        
    }

    public function consume($queue, callable $callback)
    {
        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            function ($msg) use ($callback) {
                try {
                    $data = json_decode($msg->body, true);

                    $callback($data);

                    $msg->ack();
                } catch (\Throwable $e) {
                    // log error
                    logger()->error($e->getMessage());

                    // reject and requeue false (avoid infinite loop)
                    $msg->nack(false, false);
                }
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }


    public function setup()
    {
        // Exchanges
        $this->channel->exchange_declare('order_exchange', 'direct', false, true, false);
        $this->channel->exchange_declare('user_exchange', 'direct', false, true, false);

        // Queues
        $this->channel->queue_declare('user_queue', false, true, false, false);
        $this->channel->queue_declare('order_queue', false, true, false, false);

        // Bindings
        $this->channel->queue_bind('user_queue', 'order_exchange', 'order.created');
        $this->channel->queue_bind('order_queue', 'user_exchange', 'user.validated');
    }

    public function __destruct()
    {
        try {
            $this->channel?->close();
            $this->connection?->close();
        } catch (\Throwable $e) {
        }
    }
}