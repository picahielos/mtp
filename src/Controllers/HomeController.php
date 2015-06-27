<?php

/**
 * This controller serves the home page which contains the graph.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 * @package MTP\Controllers
 */

namespace MTP\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    /**
     * @param Application $app
     * @return Response
     */
    public function get(Application $app)
    {
        return $app['twig']->render('home.twig');
    }
}
