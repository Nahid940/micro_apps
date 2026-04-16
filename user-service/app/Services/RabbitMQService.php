<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// AMQPStreamConnection
class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('services.rabbitmq.host'),
            config('services.rabbitmq.port'),
            config('services.rabbitmq.user'),
            config('services.rabbitmq.password')
        );

        $this->channel = $this->connection->channel();
    }

    public function publish($exchange, $routingKey, array $data)
    {
        $msg = new AMQPMessage(
            json_encode($data),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, $exchange, $routingKey);
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
}