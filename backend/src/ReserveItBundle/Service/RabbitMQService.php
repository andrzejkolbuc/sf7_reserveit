<?php

namespace App\ReserveItBundle\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private ?AMQPStreamConnection $connection = null;
    private const QUEUE_NAME = 'reservations';

    public function __construct(
        private string $host = 'rabbitmq',
        private int $port = 5672,
        private string $user = 'guest',
        private string $password = 'guest'
    ) {
    }

    private function getConnection(): AMQPStreamConnection
    {
        if ($this->connection === null) {
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password
            );
        }
        return $this->connection;
    }

    public function publishReservationMessage(array $message): void
    {
        $channel = $this->getConnection()->channel();
        
        // Declare the queue
        $channel->queue_declare(
            self::QUEUE_NAME,
            false,
            true,    // durable
            false,
            false
        );

        // Create and publish the message
        $msg = new AMQPMessage(
            json_encode($message),
            [
                'delivery_mode' => 2, // Persistent delivery mode
                'content_type' => 'application/json'
            ]
        );

        $channel->basic_publish($msg, '', self::QUEUE_NAME);

        $channel->close();
    }

    public function __destruct()
    {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }
}
