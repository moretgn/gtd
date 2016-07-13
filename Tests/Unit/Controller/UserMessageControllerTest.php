<?php
namespace ThomasWoehlke\Gtd\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class UserMessageControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \ThomasWoehlke\Gtd\Controller\UserMessageController
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = $this->getMock(\ThomasWoehlke\Gtd\Controller\UserMessageController::class,
            ['redirect', 'forward', 'addFlashMessage'], [], '', false);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listAction(){
        //setup some Test Data
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userOther1 = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('otheruser1','fd85df6575');
        $currentContext = new \ThomasWoehlke\Gtd\Domain\Model\Context();
        $currentContext->setNameDe('Arbeit');
        $currentContext->setNameEn('Work');
        $contextList=array($currentContext);
        $project1 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project1->setName('p1');
        $project1->setDescription('d1');
        $project2 = new \ThomasWoehlke\Gtd\Domain\Model\Project();
        $project2->setName('p2');
        $project2->setDescription('d2');
        $rootProjects = array($project1,$project2);

        $userMessage = new \ThomasWoehlke\Gtd\Domain\Model\UserMessage();
        $userMessage->setMessageText('Hello World Test');
        $userMessage->setReceiver($userLoggedIn);

        $userMessages = array($userMessage);

        //inject $contextService
        $contextService = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['getCurrentContext','getContextList'], [], '', false);
        $contextService->expects(self::once())->method('getCurrentContext')->will(self::returnValue($currentContext));
        $contextService->expects(self::once())->method('getContextList')->will(self::returnValue($contextList));
        $this->inject($this->subject, 'contextService', $contextService);

        //inject $userMessageRepository
        $userMessageRepository = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['findAllBetweenTwoUsers','update'], [], '', false);
        $userMessageRepository->expects(self::once())->method('findAllBetweenTwoUsers')->will(self::returnValue($userMessages));
        $userMessageRepository->expects(self::once())->method('update')->with($userMessage);
        $this->inject($this->subject, 'userMessageRepository', $userMessageRepository);

        //inject $projectRepository
        $projectRepository = $this->getMock(\ThomasWoehlke\Gtd\Domain\Repository\ProjectRepository::class, ['getRootProjects'], [$currentContext], '', false);
        $projectRepository->expects(self::once())->method('getRootProjects')->will(self::returnValue($rootProjects));
        $this->inject($this->subject, 'projectRepository', $projectRepository);

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $view->expects(self::at(0))->method('assign')->withConsecutive(['thisUser',$userLoggedIn]);
        $view->expects(self::at(1))->method('assign')->withConsecutive(['otherUser',$userOther1]);
        $view->expects(self::at(2))->method('assign')->withConsecutive(['userMessages',$userMessages]);
        $view->expects(self::at(3))->method('assign')->withConsecutive(['contextList',$contextList]);
        $view->expects(self::at(4))->method('assign')->withConsecutive(['currentContext',$currentContext]);
        $view->expects(self::at(5))->method('assign')->withConsecutive(['rootProjects',$rootProjects]);

        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction($userLoggedIn,$userOther1);
    }

    /**
     * @test
     */
    public function createAction(){
        //setup some Test Data
        $userLoggedIn = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('loggedinuser','fd85df6575');
        $userOther1 = new \TYPO3\CMS\Extbase\Domain\Model\FrontendUser('otheruser1','fd85df6575');
        $userMessage = new \ThomasWoehlke\Gtd\Domain\Model\UserMessage();
        $userMessage->setMessageText('Hello World Test');
        $userMessage->setReceiver($userLoggedIn);
        $userMessage->setSender($userOther1);

        //inject $userMessageRepository
        $userMessageRepository = $this->getMock(\ThomasWoehlke\Gtd\Service\ContextService::class, ['add'], [], '', false);
        $userMessageRepository->expects(self::once())->method('add')->with($userMessage);
        $this->inject($this->subject, 'userMessageRepository', $userMessageRepository);

        $this->subject->createAction($userMessage,$userLoggedIn,$userOther1);
    }
}
