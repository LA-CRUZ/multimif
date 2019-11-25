<?php

namespace App\Tests\Entity;

use App\Entity\Quiz;
use App\Entity\Question;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class QuizTest extends TestCase
{
    private static $quizReflection;
    private $quiz;

    public static function setUpBeforeClass()
    {
        self::$quizReflection = new \ReflectionClass('App\Entity\Quiz');
    }

    protected function setUp()
    {
        $this->quiz = new Quiz();
    }
    
    public function test__construct()
    {
        $property = self::$quizReflection->getProperty('questions');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue($this->quiz));
    }

    public function testGetId()
    {
        $testValue = 1;
        $property = self::$quizReflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->quiz,$testValue);

        $this->assertEquals($this->quiz->getId(),$testValue);
    }

    public function testGetTitre()
    {
        $testValue = "1";
        $property = self::$quizReflection->getProperty('titre');
        $property->setAccessible(true);
        $property->setValue($this->quiz,$testValue);

        $this->assertEquals($this->quiz->getTitre(),$testValue);
    }

    public function testSetTitre()
    {
        $testValue = "1";
        $returnValue = $this->quiz->setTitre($testValue);
        $property = self::$quizReflection->getProperty('titre');
        $property->setAccessible(true);
        
        $this->assertEquals($property->getValue($this->quiz),$testValue);
        $this->assertEquals($returnValue,$this->quiz);
    }

    public function testGetDescription()
    {
        $testValue = "1";
        $property = self::$quizReflection->getProperty('description');
        $property->setAccessible(true);
        $property->setValue($this->quiz,$testValue);

        $this->assertEquals($this->quiz->getDescription(),$testValue);
    }

    public function testSetDescription()
    {
        $testValue = "1";
        $returnValue = $this->quiz->setDescription($testValue);
        $property = self::$quizReflection->getProperty('description');
        $property->setAccessible(true);
        
        $this->assertEquals($property->getValue($this->quiz),$testValue);
        $this->assertEquals($returnValue,$this->quiz);
    }

    public function testGetQuestions()
    {
        $testValue = new ArrayCollection();
        $property = self::$quizReflection->getProperty('questions');
        $property->setAccessible(true);
        $property->setValue($this->quiz,$testValue);

        $this->assertEquals($this->quiz->getQuestions(),$testValue);
    }

    public function testAddQuestion()
    {
        $testValue = new Question();
        $returnValue = $this->quiz->addQuestion($testValue);
        $property = self::$quizReflection->getProperty('questions');
        $property->setAccessible(true);

        $this->assertContains($testValue,$property->getValue($this->quiz));
        $this->assertEquals($returnValue,$this->quiz);

        $count = $property->getValue($this->quiz)->count();
        $this->quiz->addQuestion($testValue);
        $this->assertEquals($count,$property->getValue($this->quiz)->count());
    }

    public function testRemoveQuestion()
    {
        $testValue = new Question();
        $property = self::$quizReflection->getProperty('questions');
        $property->setAccessible(true);
        $property->getValue($this->quiz)->add($testValue);
        $returnValue = $this->quiz->removeQuestion($testValue);
        
        $this->assertNotContains($testValue,$property->getValue($this->quiz));
        $this->assertEquals($returnValue,$this->quiz);
    }

}

?>