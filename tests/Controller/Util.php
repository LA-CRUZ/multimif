<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectManager;

class Util
{
    public static function prepareObjectManagerMock($objectManagerMock, bool $dbAccess)
    {
        $call = $dbAccess ? TestCase::atLeastOnce() : TestCase::never();

        $objectManagerMock
            ->expects($call)
            ->method('persist');
        $objectManagerMock
            ->expects($call)
            ->method('flush');
        
        return $objectManagerMock;
    }

    public static function prepareFormMock($formMock, $isSubmittedReturnValue,$isValidReturnValue)
    {
        $formMock
            ->expects(TestCase::once())
            ->method('handleRequest');
        $formMock
            ->expects(TestCase::any())
            ->method('isSubmitted')
            ->willReturn($isSubmittedReturnValue);
        $formMock
            ->expects(TestCase::any())
            ->method('isValid')
            ->willReturn($isValidReturnValue);

        return $formMock;
    }

    public static function setFinalCall($controller,$action,$param,$attributes=null)
    {
        if(is_null($action))
        {
            return;
        }
        $controller
            ->expects(TestCase::once())
            ->method($action)
            ->with(
                TestCase::equalTo($param),
                is_null($attributes) ? TestCase::anything() : $attributes
            );
    }

    public static function flash($controller,bool $flash)
    {
        $controller
            ->expects($flash ? TestCase::once() : TestCase::never())
            ->method('addFlash');
    }
}