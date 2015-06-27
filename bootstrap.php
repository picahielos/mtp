<?php

/**
 * Initialises Application object with all the services.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 */

use Silex\Application;
use PhpAmqpLib\Connection\AMQPConnection;
use MTP\Message\MessageQueue;
use MTP\Message\MessageProcessor;
use MTP\Models\TransactionDbModel;
use Silex\Provider\MonologServiceProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

// register monolog
$app->register(
    new MonologServiceProvider(),
    ['monolog.logfile' => __DIR__ . '/mtp.log']
);

// add services
$app['rabbitmq'] = $app->share(function () {
    return new AMQPConnection('localhost', 5672, 'guest', 'guest');
});
$app['db'] = $app->share(function () {
    return new PDO('mysql:host=localhost;dbname=mtp', 'root', '');
});
$app['MessageQueue'] = $app->share(function () use ($app) {
    return new MessageQueue($app['rabbitmq']);
});
$app['TransactionDbModel'] = $app->share(function () use ($app) {
    return new TransactionDbModel($app['db']);
});
$app['MessageProcessor'] = $app->share(function () use ($app) {
    return new MessageProcessor($app['TransactionDbModel'], $app['monolog']);
});

return $app;
