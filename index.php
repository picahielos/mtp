<?php

use Silex\Provider\TwigServiceProvider;

$app = require_once __DIR__ . '/bootstrap.php';

// registers twig service
$app->register(new TwigServiceProvider(), ['twig.path' => __DIR__.'/views']);

// controllers
$app->post('transactions', 'MTP\Controllers\TransactionController::post');
$app->get('transactions', 'MTP\Controllers\TransactionController::get');
$app->get('', 'MTP\Controllers\HomeController::get');

$app->run();
