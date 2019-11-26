<?php

namespace App\Tests\Controller;

use App\Controller\MainController;
use App\Tests\Controller\AbstractControllerTestCase;
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

class MainControllerTest extends AbstractControllerTestCase
{
    protected function setUp()
    {
        $this->controllerMock = 
            $this
                ->getMockBuilder(MainController::class)
                ->setMethods(array('createForm','redirectToRoute','render','getDoctrine','numhash','getUser','addFlash'))
                ->getMock();
    
        parent::setUp();

        $resultRepoMock = $this->getMockBuilder(ResultRepository::class)
            ->setMethods(['findByresponse'])
            ->disableOriginalConstructor()
            ->getMock();

        $classes = [
                        Quiz::class => $this->createMock(QuizRepository::class),
                        Result::class => $resultRepoMock,
                        Question::class => $this->createMock(QuestionRepository::class)
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

        $this->managerRegistryMock->getManager()
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValueMap($map2));
    }

    public function testIndex()//1
    {
        $action = 'render';
        include __DIR__ . "/../RoutesAndTwigs.php";
        $this->controllerMock->expects($this->once())
            ->method($action)
            ->with(
                $this->equalTo($indexTwig),$this->anything());
        $this->controllerMock->index();
    }
    
    public function createDataProvider()
    {
        include __DIR__ . "/../RoutesAndTwigs.php";
        //form submitted, form valid, database actions, action, param
        $cases = [
            [FALSE,FALSE,[],'render',$quizCreateTwig],
            [TRUE,FALSE,[],'render',$quizCreateTwig],
            [FALSE,TRUE,[],'render',$quizCreateTwig],
            [TRUE,TRUE,['persist' => 1],'redirectToRoute',$createQuestionRoute]
        ];
        
        return $cases;
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($formSubmitted,$formValid,$dbActions,$action,$param)//9
    {
        //TODO treat case true
        $quizEnds = false;
        $this->prepareObjectManagerMock($dbActions);

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
        
        $request = $this->createMock(Request::class);
        $this->prepareFormMock($formSubmitted,$formValid,$request);

        $this->setFinalCall($action,$param);

        $this->controllerMock->create($request);
    }

    public function createQuestionDataProvider()
    {
        include __DIR__ . "/../RoutesAndTwigs.php";

        $fail = [[],'render',$questionCreateTwig,FALSE];
        //form submitted, form valid, addIsClicked, database actions, action, param
        $cases = [
            array_merge([FALSE,FALSE,FALSE],$fail),
            array_merge([FALSE,FALSE,TRUE],$fail),
            array_merge([FALSE,TRUE,FALSE],$fail),
            array_merge([FALSE,TRUE,TRUE],$fail),
            array_merge([TRUE,FALSE,FALSE],$fail),
            array_merge([TRUE,FALSE,TRUE],$fail),
            array_merge([TRUE,TRUE,FALSE],[['persist'=>5]]+$fail),
            [TRUE,TRUE,TRUE,['persist'=>5],'redirectToRoute',$createQuestionRoute,TRUE]
        ];
        
        return $cases;
    }
    /**
     * @dataProvider createQuestionDataProvider
     */
    public function testCreateQuestion($formSubmitted,$formValid,$addIsClicked,$dbActions,$action,$param,$flash)//16
    {
        //$this->flash($flash);
        $this->setAddIsClicked($this->formInterfaceMock,$addIsClicked);

        $manager =$this->prepareObjectManagerMock($dbActions);

        $this->managerRegistryMock->getRepository(Quiz::class)
            ->expects($this->any())
            ->method('find')
            ->willReturn(new Quiz());

        $this->setFinalCall($action,$param);
        $request = $this->createMock(Request::class);
        $this->prepareFormMock($formSubmitted,$formValid);

        $this->controllerMock->createQuestion($request,0);
    }

    private function setAddIsClicked($form,$addIsClicked)
    {
        $form
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([['add',
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
                }]])
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
    public function testShow_quiz_list($action,$param)//
    {   
        $this->setFinalCall($action,$param);

        $this->controllerMock->show_quiz_list();
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
    public function testQuiz($referer,$flash)//2
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
        $this->flash($flash);
        $this->setFinalCall($action,$param,$attributes);
        $this->controllerMock->quiz($request,$quizId);
    }

    public static function quizDesc($nbQuestions,$nbReponsesPerQuestion)
    {
        $quizDesc = ['id' => 27 , 'questions' => []];

        for($nQ = 0 ; $nQ < $nbQuestions ; $nQ++)
        {
            $reponses = null;
            for($nR = 0 ; $nR < $nbReponsesPerQuestion ; $nR++)
            {
                $reponses[] = $nR+$nQ*$nbReponsesPerQuestion; 
            }
            $quizDesc['questions'][] = ['id' => $nQ , 'reponses' => $reponses];
        }

        return $quizDesc;
    }

    private function buildQuizMock($quizDesc)
    {
        $quizMock = $this->createMock(Quiz::class);
        $quizMock
            ->expects($this->any())
            ->method('getId')
            ->willReturn($quizDesc['id']);
        
        $questionsMocks = [];
        foreach($quizDesc['questions'] as $question)
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
            
            $questionMock
                ->expects($this->any())
                ->method('getQuiz')
                ->willReturn($quizMock);

            $questionsMocks[] = $questionMock;

        }
        $quizMock
            ->expects($this->any())
            ->method('getQuestions')
            ->willReturn(new ArrayCollection($questionsMocks));

        return $quizMock;
    }

    public function statDataProvider()
    {
        $baseAttributes = [
            'display' => false,
            'codeHash' => 28
        ];


        $returnarray[] = [$this->quizDesc(0,0),[],$baseAttributes];

        $nbQuestions = 4;
        $nbReponses = 3;

        $quiz = $this->quizDesc($nbQuestions,$nbReponses);

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
        $this->controllerMock
            ->expects($this->any())
            ->method('numhash')
            ->will($this->returnValueMap([[$id,$hashedId]]));
    }

    /**
     * @dataProvider statDataProvider
     */
    public function testStat($quizDesc,$nbResultsByReponse,$attributes)
    {
        include __DIR__ . "/../RoutesAndTwigs.php";

        $action = 'render';
        $param = $statTwig;

        $quizMock = $this->buildQuizMock($quizDesc);
        
        $this->setHash($quizDesc['id'],$attributes['codeHash']);
        

        $attributes['quiz'] = $quizMock;

        $this->managerRegistryMock->getRepository(Quiz::class)
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValueMap([[$quizDesc['id'],null,null,$quizMock]]));

        $reponseIdToResultMap = [];
        foreach($nbResultsByReponse as $reponseId=>$nbResults)
        {
            $reponseIdToResultMap[]= [$reponseId , array_fill(0,$nbResults,'')];
        }
        
        $this->managerRegistryMock->getRepository(Result::class)
            ->expects($this->any())
            ->method('findByresponse')
            ->will($this->returnValueMap($reponseIdToResultMap));

        
        $this->setFinalCall($action,$param,$attributes);
        $this->controllerMock->stat($quizDesc['id']);
    }

    public function answerDataProvider()
    {
        include __DIR__ . "/../RoutesAndTwigs.php";
        $reponses = [
                '0' => 'a',
                '2' => 'a',
                '7' => 'a'
            ];

        $attributes = [
            'formAnswer' => new class(){},
            'codeHash' => 28
        ];
        
        return [
            [self::quizDesc(5,5),$reponses,['persist'=>3],'redirectToRoute',$searchQuizRoute,null,TRUE],
            [self::quizDesc(5,5),[],[],'render',$answerTwig,$attributes,FALSE]
        ];
    }

    /**
     * @dataProvider answerDataProvider
     */
    public function testAnswer($quizDesc,$reponses,$dbActions,$action,$param,$attributes,$flash)
    {
        
        $quizMock = $this->buildQuizMock($quizDesc);

        $this->managerRegistryMock->getRepository(Quiz::class)
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValueMap([[$quizDesc['id'],null,null,$quizMock]]));

        if($attributes){
            $attributes['quiz'] = $quizMock;
            $this->setHash($quizDesc['id'],$attributes['codeHash']);
        }
        $request = $this->createMock(Request::class);

        $request->request = new class($reponses)
            {
                private $attrs;
                public function __construct($attrs)
                {
                    $this->attrs = $attrs;
                    //var_dump($attrs);
                }
                public function get($attrName)
                {
                    return array_key_exists($attrName,$this->attrs) ? $this->attrs[$attrName] : null;
                }
            };
        
        $this->prepareFormMock(false,false,$request,$attributes['formAnswer']);
        $this->flash($flash);
        $this->setFinalCall($action,$param,$attributes);

        $this->controllerMock->answer($request,$quizDesc['id']);
    }

    public function testRemoveQuestion()
    {
        $action = 'redirectToRoute';
        $param = 'edit-quiz';
        
        $quizMock = $this->buildQuizMock(self::quizDesc(5,5));
        $attributes = ['id'=>$quizMock->getId()];
        $questionMock = $quizMock->getQuestions()[0];
        $this->managerRegistryMock->getRepository(Question::class)
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValueMap([[$questionMock->getId(),null,null,$questionMock]]));
        
        $this->prepareObjectManagerMock(['remove'=>1]);
        $this->flash(true);
        $this->setFinalCall($action,$param,$attributes);

        $this->controllerMock->removeQuestion($this->createMock(Request::class),$questionMock->getId());
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