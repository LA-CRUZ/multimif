<?php

namespace App\Tests\Controller;

use App\Controller\MainController;
use App\Tests\Controller\Util;
use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use App\Repository\QuizRepository;
use App\Repository\ResultRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Collections\ArrayCollection;

class MainControllerTest extends TestCase
{
    private $mainControllerMock;
    private $managerRegistryMock;
    private $formInterfaceMock;
    private $userMock;

    protected function setUp()
    {
        $this->mainControllerMock = 
            $this
                ->getMockBuilder(MainController::class)
                ->setMethods(array('createForm','redirectToRoute','render','getDoctrine','numhash','getUser','addFlash'))
                ->getMock();
        
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);

        $this->mainControllerMock
            ->expects($this->any())
            ->method('getDoctrine')
            ->willReturn(
                $this->managerRegistryMock
            );
    
        $objectManagerMock = $this->createMock(ObjectManager::class);

        $this->managerRegistryMock
            ->expects($this->any())
            ->method('getManager')
            ->willReturn($objectManagerMock);

        $resultRepoMock = $this->getMockBuilder(ResultRepository::class)
            ->setMethods(['findByresponse'])
            ->disableOriginalConstructor()
            ->getMock();

        $classes = [
                        Quiz::class => $this->createMock(QuizRepository::class),
                        Result::class => $resultRepoMock,
                        Question::class => QuestionRepository::class
                    ];
        foreach($classes as $class => $repo)
        {
            $map[] = [$class , null ,$repo];
        }

        $this->managerRegistryMock
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValueMap($map));

        foreach($map as $m)
        {
            $map2[] = [$m[0] , $m[2]];
        }

        $objectManagerMock
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValueMap($map2));

        $this->formInterfaceMock = $this->createMock(FormInterface::class);

        $this->userMock = $this->createMock(User::class);

        $this->mainControllerMock
            ->expects($this->any())
            ->method('getUser')
            ->willReturn($this->userMock);
    }

    public function testIndex()
    {
        $action = 'render';
        include __DIR__ . "/../RoutesAndTwigs.php";
        $this->mainControllerMock->expects($this->once())
            ->method($action)
            ->with(
                $this->equalTo($indexTwig),$this->anything());
        $this->mainControllerMock->index();
    }

    private function setCreateFormReturnValue($form)
    {
        $this->mainControllerMock
            ->expects($this->once())
            ->method('createForm')
            ->willReturn($form);
    }

    public function createDataProvider()
    {
        include __DIR__ . "/../RoutesAndTwigs.php";
        //form submitted, form valid, database access, action, param
        $cases = [
            [FALSE,FALSE,FALSE,'render',$quizCreateTwig],
            [TRUE,FALSE,FAlSE,'render',$quizCreateTwig],
            [FALSE,TRUE,FALSE,'render',$quizCreateTwig],
            [TRUE,TRUE,TRUE,'redirectToRoute',$createQuestionRoute]
        ];
        
        return $cases;
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($formSubmitted,$formValid,$dbAccess,$action,$param)
    {
        //TODO treat case true
        $quizEnds = false;
        Util::prepareObjectManagerMock($this->managerRegistryMock->getManager(),$dbAccess);
        
        Util::prepareFormMock($this->formInterfaceMock,$formSubmitted,$formValid);

        $obj = new class($quizEnds) {
            private $ends;
            public function __construct($ends){
                $this->ends = $ends;
            }
            public function getData(){
                return $this->ends;
            }
        };
        $this->formInterfaceMock
            ->expects($this->any())
            ->method('offsetGet')
            ->will($this->returnValueMap([['end',$obj]]));

        $this->setCreateFormReturnValue($this->formInterfaceMock);

        Util::setFinalCall($this->mainControllerMock,$action,$param);

        $this->mainControllerMock->create($this->createMock(Request::class));
    }

    public function createQuestionDataProvider()
    {
        include __DIR__ . "/../RoutesAndTwigs.php";

        $fail = [FALSE,'render',$questionCreateTwig,FALSE];
        //form submitted, form valid, addIsClicked, database access, action, param
        $cases = [
            array_merge([FALSE,FALSE,FALSE],$fail),
            array_merge([FALSE,FALSE,TRUE],$fail),
            array_merge([FALSE,TRUE,FALSE],$fail),
            array_merge([FALSE,TRUE,TRUE],$fail),
            array_merge([TRUE,FALSE,FALSE],$fail),
            array_merge([TRUE,FALSE,TRUE],$fail),
            array_merge([TRUE,TRUE,FALSE],[TRUE]+$fail),
            [TRUE,TRUE,TRUE,    TRUE,'redirectToRoute',$createQuestionRoute,TRUE]
        ];
        
        return $cases;
    }
    /**
     * @dataProvider createQuestionDataProvider
     */
    public function testCreateQuestion($formSubmitted,$formValid,$addIsClicked,$dbAccess,$action,$param,$flash)
    {
        Util::prepareFormMock($this->formInterfaceMock,$formSubmitted,$formValid);
        Util::flash($this->mainControllerMock,$flash);
        $this->setAddIsClicked($this->formInterfaceMock,$addIsClicked);
        $this->setCreateFormReturnValue($this->formInterfaceMock);
        $manager = Util::prepareObjectManagerMock($this->managerRegistryMock->getManager(),$dbAccess);

        $this->managerRegistryMock->getRepository(Quiz::class)
            ->expects($this->any())
            ->method('find')
            ->willReturn(new Quiz());

        Util::setFinalCall($this->mainControllerMock,$action,$param);
        
        $this->mainControllerMock->createQuestion($this->createMock(Request::class),0);
    }

    private function setAddIsClicked($form,$addIsClicked)
    {
        $form
            ->expects($this->any())
            ->method('get')
            ->with('add')
            ->willReturn(
                new class($addIsClicked)
                {
                    private $clicked;

                    public function __construct($clicked)
                    {
                        $this->clicked = $clicked;
                    }
                    public function isClicked()
                    {
                        return $this->clicked;
                    }
                }
            );
    }

    public function show_quiz_listDataProvider()
    {
        include __DIR__ . "/../RoutesAndTwigs.php";
        //action,param
        return [
            ['render',$quizListTwig]
        ];
    }

    /**
     * @dataProvider show_quiz_listDataProvider
     */
    public function testShow_quiz_list($action,$param)
    {   
        Util::setFinalCall($this->mainControllerMock,$action,$param);

        $this->mainControllerMock->show_quiz_list();
    }

    public function quizDataProvider()
    {
        
        return [
            ['',FALSE],
            ['create_question',TRUE]
        ];
    }
    /**
     * @dataProvider quizDataProvider
     */
    public function testQuiz($referer,$flash)
    {
        include __DIR__ . "/../RoutesAndTwigs.php";

        $action = 'render';
        $param = $quizTwig;
        $quizId = 27;
        $hashedQuizId = 28;
        $this->setHash($quizId,$hashedQuizId);
        $quizMock=$this->createMock(Quiz::class);
        $attributes = [
            'controller_name' => 'MainController',
            'quiz' => $quizMock,
            'codeHash' => $hashedQuizId
        ];

        $this->managerRegistryMock->getRepository(Quiz::class)
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValueMap([[$quizId,null,null,$quizMock]]));
        
        $request = $this->createMock(Request::class);
        $request->headers = new class($referer)
            {
                private $attrs;
                public function __construct($referer)
                {

                    $this->attrs['referer'] = $referer;
                }
                public function get($attrName)
                {
                    return $this->attrs[$attrName];
                }
            };
        var_dump($request->headers);
        Util::flash($this->mainControllerMock,$flash);
        Util::setFinalCall($this->mainControllerMock,$action,$param,$attributes);
        $this->mainControllerMock->quiz($request,$quizId);
    }

    public function statDataProvider()
    {
        $baseAttributes = [
            'display' => false,
            'codeHash' => 27
        ];

        $quiz = ['id' => 0 , 'questions' => []];

        $returnarray[] = [$quiz,[],$baseAttributes];

        $nbQuestions = 4;
        $nbReponses = 3;

        foreach(range(0,$nbQuestions-1) as $nQ)
        {
            $reponses = null;
            foreach(range(0,$nbReponses-1) as $nR)
            {
                $reponses[] = $nR+$nQ*$nbReponses; 
            }
            $questions[] = ['id' => $nQ , 'reponses' => $reponses];
        }
        $quiz = ['id' => 0 , 'questions' => $questions];

        $returnarray[]= [$quiz,array_fill(0,$nbQuestions*$nbReponses,0),$baseAttributes];

        $nbResultsByReponse = [34,3,48,95,59,40,30,20,48,38,36,73];

        $nbResultsByQuestion = [85,194,98,147];

        $percentages = [40,4,56,49,30,21,31,20,49,26,24,50];
        
        $successAttributes = [
            'display' => true,
            'controller_name' => 'MainController',
            'stat' => $nbResultsByReponse,
            'total' => $nbResultsByQuestion,
            'pourcent' => $percentages
        ] + $baseAttributes;
        
        $returnarray[] =
            [$quiz,$nbResultsByReponse,$successAttributes];

        return $returnarray;
    }

    private function setHash($id,$hashedId)
    {
        $this->mainControllerMock
            ->expects($this->any())
            ->method('numhash')
            ->will($this->returnValueMap([[$id,$hashedId]]));
    }

    /**
     * @dataProvider statDataProvider
     */
    public function testStat($quiz,$nbResultsByReponse,$attributes)
    {
        include __DIR__ . "/../RoutesAndTwigs.php";

        $action = 'render';
        $param = $statTwig;

        $quizMock = $this->createMock(Quiz::class);
        $quizMock
            ->expects($this->any())
            ->method('getId')
            ->willReturn($quiz['id']);
        
        $this->setHash($quiz['id'],$attributes['codeHash']);
        
        $questionsMocks = [];
        foreach($quiz['questions'] as $question)
        {
            $questionMock = $this->createMock(Question::class);
            $questionMock
                ->expects($this->any())
                ->method('getId')
                ->willReturn($question['id']);
            
            $reponses = null;
            foreach($question['reponses'] as $reponseId)
            {
                $reponseMock = $this->createMock(Reponse::class);
                $reponseMock
                    ->expects($this->any())
                    ->method('getId')
                    ->willReturn($reponseId);
                $reponses[]= $reponseMock;  
            }
            $questionMock
                    ->expects($this->any())
                    ->method('getReponses')
                    ->willReturn(new ArrayCollection($reponses));
            
            $questionsMocks[] = $questionMock;
        }
        $quizMock
            ->expects($this->any())
            ->method('getQuestions')
            ->willReturn(new ArrayCollection($questionsMocks));

        $attributes['quiz'] = $quizMock;

        $this->managerRegistryMock->getRepository(Quiz::class)
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValueMap([[$quiz['id'],null,null,$quizMock]]));

        $reponseIdToResultMap = [];
        foreach($nbResultsByReponse as $reponseId=>$nbResults)
        {
            $reponseIdToResultMap[]= [$reponseId , array_fill(0,$nbResults,'')];
        }
        //var_dump($reponseIdToResultMap);
        $this->managerRegistryMock->getRepository(Result::class)
            ->expects($this->any())
            ->method('findByresponse')
            ->will($this->returnValueMap($reponseIdToResultMap));

        $this->mainControllerMock
            ->expects($this->once())
            ->method($action)
            ->with(
                $this->equalTo($param),
                $this->equalTo($attributes)
            );

        $this->mainControllerMock->stat($quiz['id']);
    }

    public function testAnswer()
    {
        $this->markTestSkipped();
    }

    public function testRemoveQuestion()
    {
        $this->markTestSkipped();    
    }
    

    public function testEditQuestion()
    {
        $this->markTestSkipped();
    }

    public function testSearch()
    {
        $this->markTestSkipped();
    }
}

?>