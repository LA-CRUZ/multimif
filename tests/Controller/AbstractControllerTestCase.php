<?php

namespace App\Tests\Controller;

use App\Entity\User;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractControllerTestCase extends TestCase
{
    protected $controllerMock;
    protected $managerRegistryMock;
    protected $userMock;
    protected $formInterfaceMock;

    protected function setUp()
    {
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $objectManagerMock = $this->createMock(ObjectManager::class);

        $this->managerRegistryMock
            ->expects($this->any())
            ->method('getManager')
            ->willReturn($objectManagerMock);

        $this->controllerMock
            ->expects($this->any())
            ->method('getDoctrine')
            ->willReturn(
                $this->managerRegistryMock
            );

        //set return for getUser()
        $this->userMock = $this->createMock(User::class);
        $this->controllerMock
            ->expects($this->any())
            ->method('getUser')
            ->willReturn($this->userMock);

        $this->formInterfaceMock = $this->createMock(FormInterface::class);
    }

    protected function prepareObjectManagerMock(array $expectedActions)
    {   
        $possibleActions = ['persist','remove'];
        
        $objectManagerMock = $this->managerRegistryMock->getManager();
        foreach($possibleActions as $action)
        {
            $objectManagerMock
            ->expects(array_key_exists($action,$expectedActions) ? $this->exactly($expectedActions[$action]) : $this->never())
            ->method($action);
        }
        
        $objectManagerMock
            ->expects($expectedActions ? $this->once() : $this->any())
            ->method('flush');
    }

    protected function prepareFormMock($isSubmittedReturnValue,$isValidReturnValue,$request=null,$view=null)
    {
        $this->formInterfaceMock
            ->expects($this->any())
            ->method('isSubmitted')
            ->willReturn($isSubmittedReturnValue);
        $this->formInterfaceMock
            ->expects($this->any())
            ->method('isValid')
            ->willReturn($isValidReturnValue);
        if($request){
            $this->formInterfaceMock
                ->expects($this->any())
                ->method('handleRequest')
                ->with($this->equalTo($request));
        }
        if($view){
            $this->formInterfaceMock
                ->expects($this->any())
                ->method('createView')
                ->willReturn($view);
        }

        $this->controllerMock
            ->expects($this->any())
            ->method('createForm')
            ->willReturn($this->formInterfaceMock);
    }

    protected function setFinalCall($action,$param,$attributes=null)
    {
        if(is_null($action))
        {
            return;
        }
        $this->controllerMock
            ->expects($this->once())
            ->method($action)
            ->with(
                $this->equalTo($param),
                is_null($attributes) ? $this->anything() : $this->equalTo($attributes)
            );
    }

    protected function flash(bool $flash)
    {
        $this->controllerMock
            ->expects($flash ? $this->once() : $this->never())
            ->method('addFlash');
    }
}