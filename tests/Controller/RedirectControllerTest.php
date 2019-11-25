<?php

namespace App\Tests\Controller;

use App\Controller\RedirectController;
use PHPUnit\Framework\TestCase;

class RedirectControllerTest extends TestCase
{
    private $homeRoute = 'home';

    public function testHome()
    {
        $mock = $this->getMockBuilder(RedirectController::class)
            ->setMethods(['redirectToRoute'])
            ->getMock();
        
        $mock->expects($this->once())
            ->method('redirectToRoute')
            ->with($this->equalTo($this->homeRoute));
        
        $redirectController = $mock;

        $redirectController->home();
    }

}

?>