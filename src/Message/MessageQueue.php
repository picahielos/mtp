<?php

/**
 * RabbitMQ queue implementation.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 * @package MTP\Message
 */

namespace MTP\Message;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

class MessageQueue
{
    /**
     * @const string
     */
    const QUEUE_NAME = 'message_queue';

    /**
     * @var AMQPConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @param AMQPConnection $connection
     */
    public function __construct(AMQPConnection $connection)
    {
        $this->connection = $connection;
        $this->channel = $connection->channel();

        $this->channel->queue_declare(self::QUEUE_NAME, false, true, false, false);
    }

    /**
     * Sends the string $data as a message to the queue.
     *
     * @param string $data
     */
    public function sendMessage($data)
    {
        $message = new AMQPMessage($data, ['delivery_mode' => 2]);

        $this->channel->basic_publish($message, '', self::QUEUE_NAME);
    }

    /**
     * Infinite loop: Listens for messages from the queue and sends them to the callback.
     *
     * @param callback $callback
     */
    public function listen($callback)
    {
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(self::QUEUE_NAME, '', false, true, false, false, $callback);

        while ($this->channel->callbacks) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
