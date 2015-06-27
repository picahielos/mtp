<?php

namespace MTP\Tests\Controllers;

use MTP\Controllers\HomeController;
use PHPUnit_Framework_TestCase;
use Twig_Environment;
use Silex\Application;

class HomeControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    public function setUp()
    {
        $this->twig = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGet()
    {
        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('home.twig')
            ->will($this->returnValue('rendered template'));

        $app = new Application();
        $app['twig'] = $this->twig;

        $controller = new HomeController();
        $response = $controller->get($app);

        $this->assertEquals($response, 'rendered template');
    }
}
