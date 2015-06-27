<?php

/**
 * This controller accepts transaction messages by post and shows a json with them.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 * @package MTP\Controllers
 */

namespace MTP\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransactionController
{
    /**
     * Accepts a JSON representing a transaction. It will send it to RabbitMQ so later
     * it can be processed.
     *
     * @param Request $request
     * @param Application $app
     * @return JsonResponse
     */
    public function post(Request $request, Application $app)
    {
        $queue = $app['MessageQueue'];

        $messageData = $request->getContent();
        $queue->sendMessage($messageData);

        return $app->json();
    }

    /**
     * Gets a representation of all the transactions in DB. Depending on the volume
     * we should consider to paginate it or filter by time.
     *
     * @param Application $app
     * @return JsonResponse
     */
    public function get(Application $app)
    {
        $model = $app['TransactionDbModel'];

        $transactions = $model->get();

        return $app->json($transactions);
    }
}
