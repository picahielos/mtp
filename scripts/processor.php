<?php

/**
 * This script listens to the RabbitMQ queue for new transactions and processes them
 * with MessageProcessor.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 * @package scripts
 */

$app = require_once __DIR__ . '/../bootstrap.php';

$queue = $app['MessageQueue'];
$processor = $app['MessageProcessor'];

// sends $processor->process() as callback to the queue
$queue->listen(array($processor, 'process'));
