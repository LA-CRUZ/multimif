<?php

namespace App\Tests\Entity;

use App\Entity\Reponse;
use App\Entity\Question;
use PHPUnit\Framework\TestCase;

class ReponseTest extends TestCase
{
    private static $reponseReflection;
    private $reponse;

    public static function setUpBeforeClass()
    {
        self::$reponseReflection = new \ReflectionClass('App\Entity\Reponse');
    }

    protected function setUp()
    {
        $this->reponse = new Reponse();
    }

    public function testGetId()
    {
        $testValue = 1;
        $property = self::$reponseReflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->reponse,$testValue);

        $this->assertEquals($this->reponse->getId(),$testValue);
    }

    public function testGetTexte()
    {
        $property = self::$reponseReflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->reponse,"1");

        $this->assertEquals($this->reponse->getId(),"1");
    }

    public function testSetTexte()
    {
        $testValue = "1";
        $this->reponse->setTexte($testValue);
        $property = self::$reponseReflection->getProperty('texte');
        $property->setAccessible(true);
        
        $this->assertEquals($property->getValue($this->reponse),$testValue);
    }

    public function testGetJuste()
    {
        $testValue = true;
        $property = self::$reponseReflection->getProperty('juste');
        $property->setAccessible(true);
        $property->setValue($this->reponse,$testValue);

        $this->assertEquals($this->reponse->getJuste(),$testValue);
    }

    public function testSetJuste()
    {
        $testValue = true;
        $returnValue = $this->reponse->setJuste($testValue);
        $property = self::$reponseReflection->getProperty('juste');
        $property->setAccessible(true);
        
        $this->assertEquals($property->getValue($this->reponse),$testValue);
        $this->assertEquals($returnValue,$this->reponse);
    }

    public function testGetQuestion()
    {
        $testValue = new Question();
        $property = self::$reponseReflection->getProperty('question');
        $property->setAccessible(true);
        $property->setValue($this->reponse,$testValue);

        $this->assertEquals($this->reponse->getQuestion(),$testValue);
    }

    public function testSetQuestion()
    {
        $testValue = new Question();
        $returnValue = $this->reponse->setQuestion($testValue);
        $property = self::$reponseReflection->getProperty('question');
        $property->setAccessible(true);
        
        $this->assertEquals($property->getValue($this->reponse),$testValue);
        $this->assertEquals($returnValue,$this->reponse);
    }

}

?>