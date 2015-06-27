<?php

namespace MTP\Tests\Controllers;

use MTP\Controllers\TransactionController;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class TransactionControllerTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $app = new Application();
        $app['TransactionDbModel'] = $this->getMockBuilder('\MTP\Models\TransactionDbModel')
            ->disableOriginalConstructor()
            ->getMock();
        $app['TransactionDbModel']
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue(['someTransactions']));

        $expected = $app->json(['someTransactions']);

        $controller = new TransactionController();
        $response = $controller->get($app);

        $this->assertEquals($response, $expected);
    }

    public function testPost()
    {
        $content = '{"some": "key"}';
        $request = new Request(array(), array(), array(), array(), array(), array(), $content);

        $app = new Application();
        $app['MessageQueue'] = $this->getMockBuilder('\MTP\Message\MessageQueue')
            ->disableOriginalConstructor()
            ->getMock();
        $app['MessageQueue']
            ->expects($this->once())
            ->method('sendMessage')
            ->with($content);

        $expected = $app->json();

        $controller = new TransactionController();
        $response = $controller->post($request, $app);

        $this->assertEquals($response, $expected);
    }
}
