<?php

/**
 * Processes a JSON representing a transaction, sanitizing it and saving it into DB.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 * @package MTP\Message
 */

namespace MTP\Message;

use Exception;
use MTP\Transaction;
use MTP\Models\TransactionDbModel;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Logger;

class MessageProcessor
{
    /**
     * @var TransactionDbModel
     */
    private $messageDbModel;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param TransactionDbModel $messageDbModel
     * @param Logger $logger
     */
    public function __construct(TransactionDbModel $messageDbModel, Logger $logger)
    {
        $this->messageDbModel = $messageDbModel;
        $this->logger = $logger;
    }

    /**
     * Gets a message from RabbitMQ, checks it's a valid transaction and saves it into DB.
     *
     * @param AMQPMessage $rawMessage
     */
    public function process(AMQPMessage $rawMessage)
    {
        $this->logger->debug('Processing message ' . $rawMessage->body);

        try {
            $message = Transaction::extractFromJson($rawMessage->body);
            $message->checkSanity();

            $this->messageDbModel->save($message);
        } catch (Exception $e) {
            // either transaction contains invalid data or there was an error in db
            $this->logger->warn($e->getMessage());
        }
    }
}
