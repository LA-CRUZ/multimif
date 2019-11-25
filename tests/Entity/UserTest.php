<?php

namespace App\Tests\Entity;

use App\Entity\User;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private static $userReflection;
    private $user;

    public static function setUpBeforeClass()
    {
        self::$userReflection = new \ReflectionClass('App\Entity\User');
    }

    protected function setUp()
    {
        $this->user = new User();
    }

    public function testGetId()
    {
        $testValue = 1;
        $property = self::$userReflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->user,$testValue);

        $this->assertEquals($this->user->getId(),$testValue);
    }

    public function testGetEmail()
    {
        $testValue = "1";
        $property = self::$userReflection->getProperty('email');
        $property->setAccessible(true);
        $property->setValue($this->user,$testValue);
        
        $this->assertEquals($this->user->getEmail(),$testValue);
    }

    public function testSetEmail()
    {
        $testValue = "1";
        $returnValue = $this->user->setEmail($testValue);
        $property = self::$userReflection->getProperty('email');
        $property->setAccessible(true);

        $this->assertEquals($property->getValue($this->user),$testValue);
        $this->assertEquals($returnValue,$this->user);
    }

    public function testGetUsername()
    {
        $testValue = "1";
        $property = self::$userReflection->getProperty('username');
        $property->setAccessible(true);
        $property->setValue($this->user,$testValue);
        
        $this->assertEquals($this->user->getUsername(),$testValue);
    }

    public function testSetUsername()
    {
        $testValue = "1";
        $returnValue = $this->user->setUsername($testValue);
        $property = self::$userReflection->getProperty('username');
        $property->setAccessible(true);

        $this->assertEquals($property->getValue($this->user),$testValue);
        $this->assertEquals($returnValue,$this->user);
    }

    public function testGetPassword()
    {
        $testValue = "1";
        $property = self::$userReflection->getProperty('password');
        $property->setAccessible(true);
        $property->setValue($this->user,$testValue);
        
        $this->assertEquals($this->user->getPassword(),$testValue);
    }

    public function testSetPassword()
    {
        $testValue = "1";
        $returnValue = $this->user->setPassword($testValue);
        $property = self::$userReflection->getProperty('password');
        $property->setAccessible(true);

        $this->assertEquals($property->getValue($this->user),$testValue);
        $this->assertEquals($returnValue,$this->user);
    }

}

?>