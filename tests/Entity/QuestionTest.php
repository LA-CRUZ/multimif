<?php

namespace App\Tests\Entity;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Reponse;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class QuestionTest extends TestCase
{
    private static $questionReflection;
    private $question;

    public static function setUpBeforeClass()
    {
        self::$questionReflection = new \ReflectionClass('App\Entity\Question');
    }

    protected function setUp()
    {
        $this->question = new Question();
    }
    
    public function test__construct()
    {
        $property = self::$questionReflection->getProperty('reponses');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue($this->question));
    }

    public function testGetId()
    {
        $testValue = 1;
        $property = self::$questionReflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->question,$testValue);

        $this->assertEquals($this->question->getId(),$testValue);
    }

    public function testGetTexte()
    {
        $property = self::$questionReflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->question,"1");

        $this->assertEquals($this->question->getId(),"1");
    }

    public function testSetTexte()
    {
        $testValue = "1";
        $returnValue = $this->question->setTexte($testValue);
        $property = self::$questionReflection->getProperty('texte');
        $property->setAccessible(true);
        
        $this->assertEquals($property->getValue($this->question),$testValue);
        $this->assertEquals($returnValue,$this->question);
    }

    public function testGetQuiz()
    {
        $testValue = new Quiz();
        $property = self::$questionReflection->getProperty('quiz');
        $property->setAccessible(true);
        $property->setValue($this->question,$testValue);

        $this->assertEquals($this->question->getQuiz(),$testValue);
    }

    public function testSetQuiz()
    {
        $testValue = new Quiz();
        $returnValue = $this->question->setQuiz($testValue);
        $property = self::$questionReflection->getProperty('quiz');
        $property->setAccessible(true);
        
        $this->assertEquals($property->getValue($this->question),$testValue);
        $this->assertEquals($returnValue,$this->question);
    }

    public function testGetReponses()
    {
        $testValue = new ArrayCollection();
        $property = self::$questionReflection->getProperty('quiz');
        $property->setAccessible(true);
        $property->setValue($this->question,$testValue);

        $this->assertEquals($this->question->getReponses(),$testValue);
    }

    public function testAddReponse()
    {
        $testValue = new Reponse();
        $returnValue = $this->question->addReponse($testValue);
        $property = self::$questionReflection->getProperty('reponses');
        $property->setAccessible(true);

        $this->assertContains($testValue,$property->getValue($this->question));
        $this->assertEquals($returnValue,$this->question);

        $count = $property->getValue($this->question)->count();
        $this->question->addReponse($testValue);
        $this->assertEquals($count,$property->getValue($this->question)->count());
    }

    public function testRemoveReponse()
    {
        $testValue = new Reponse();
        $property = self::$questionReflection->getProperty('reponses');
        $property->setAccessible(true);
        $property->getValue($this->question)->add($testValue);
        $returnValue = $this->question->removeReponse($testValue);
        
        $this->assertNotContains($testValue,$property->getValue($this->question));
        $this->assertEquals($returnValue,$this->question);
    }

}

?>