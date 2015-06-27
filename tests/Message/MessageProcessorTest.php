<?php

namespace MTP\Tests\Message;

use Exception;
use Monolog\Logger;
use PHPUnit_Framework_TestCase;
use PhpAmqpLib\Message\AMQPMessage;
use MTP\Models\TransactionDbModel;
use MTP\Message\MessageProcessor;
use MTP\Transaction;

class MessageProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TransactionDbModel
     */
    private $transactionDbModel;

    /**
     * @var Logger
     */
    private $logger;

    public function setUp()
    {
        $this->transactionDbModel = $this->getMockBuilder('\MTP\Models\TransactionDbModel')
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder('\Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testErrorFromModel()
    {
        $json = <<<JSON
{
    "userId": "134256",
    "currencyFrom": "EUR",
    "currencyTo": "GBP",
    "amountSell": 1000,
    "amountBuy": 747.1,
    "rate": 0.7471,
    "timePlaced": "24-JAN-15 10:27:44",
    "originatingCountry": "FR"
}
JSON;

        $queueMessage = new AMQPMessage($json);

        $transaction = new Transaction();
        $transaction->userId = 134256;
        $transaction->currencyFrom = 'EUR';
        $transaction->currencyTo = 'GBP';
        $transaction->amountSell = 1000;
        $transaction->amountBuy = 747.1;
        $transaction->rate = 0.7471;
        $transaction->timePlaced = '2015-01-24 10:27:44';
        $transaction->originatingCountry = 'FR';

        $this->transactionDbModel
            ->expects($this->once())
            ->method('save')
            ->with($transaction)
            ->will($this->throwException(new Exception()));

        $processor = new MessageProcessor($this->transactionDbModel, $this->logger);
        $processor->process($queueMessage);
    }

    public function testBadMessage()
    {
        $queueMessage = new AMQPMessage('{"some": "key"}');

        $this->transactionDbModel->expects($this->never())->method('save');

        $processor = new MessageProcessor($this->transactionDbModel, $this->logger);
        $processor->process($queueMessage);
    }

    public function testCanSave()
    {
        $json = <<<JSON
{
    "userId": "134256",
    "currencyFrom": "EUR",
    "currencyTo": "GBP",
    "amountSell": 1000,
    "amountBuy": 747.1,
    "rate": 0.7471,
    "timePlaced": "24-JAN-15 10:27:44",
    "originatingCountry": "FR"
}
JSON;

        $queueMessage = new AMQPMessage($json);

        $transaction = new Transaction();
        $transaction->userId = 134256;
        $transaction->currencyFrom = 'EUR';
        $transaction->currencyTo = 'GBP';
        $transaction->amountSell = 1000;
        $transaction->amountBuy = 747.1;
        $transaction->rate = 0.7471;
        $transaction->timePlaced = '2015-01-24 10:27:44';
        $transaction->originatingCountry = 'FR';

        $this->transactionDbModel
            ->expects($this->once())
            ->method('save')
            ->with($transaction);

        $processor = new MessageProcessor($this->transactionDbModel, $this->logger);
        $processor->process($queueMessage);
    }
}
