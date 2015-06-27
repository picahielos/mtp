<?php

namespace MTP\Tests\Message;

use PHPUnit_Framework_TestCase;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use MTP\Message\MessageQueue;

class MessageQueueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AMQPConnection
     */
    private $clientMock;

    /**
     * @var AMQPChannel
     */
    private $channelMock;

    public function setUp()
    {
        $this->channelMock = $this->getMockBuilder('\PhpAmqpLib\Channel\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $this->channelMock
            ->expects($this->once())
            ->method('queue_declare')
            ->with('message_queue', false, true, false, false);

        $this->clientMock = $this->getMockBuilder('\PhpAmqpLib\Connection\AMQPConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->clientMock
            ->expects($this->once())
            ->method('channel')
            ->will($this->returnValue($this->channelMock));
    }

    public function testCanConstructQueue()
    {
        $queue = new MessageQueue($this->clientMock);

        $this->assertInstanceOf('\MTP\Message\MessageQueue', $queue);
    }

    public function testSendMessage()
    {
        $expectedMessage = new AMQPMessage('text message', ['delivery_mode' => 2]);

        $this->channelMock
            ->expects($this->once())
            ->method('basic_publish')
            ->with($expectedMessage, '', 'message_queue');

        $queue = new MessageQueue($this->clientMock);
        $queue->sendMessage('text message');
    }

    public function testListen()
    {
        $callback = function () {
            return 'fake callback';
        };

        $this->channelMock
            ->expects($this->at(1))
            ->method('basic_qos')
            ->with(null, 1, null);
        $this->channelMock
            ->expects($this->at(2))
            ->method('basic_consume')
            ->with('message_queue', '', false, true, false, false, $callback);

        $queue = new MessageQueue($this->clientMock);
        $queue->listen($callback);
    }

    public function testDestruct()
    {
        $this->channelMock->expects($this->once())->method('close');
        $this->clientMock->expects($this->once())->method('close');

        $queue = new MessageQueue($this->clientMock);

        unset($queue);
    }
}
