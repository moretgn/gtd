<?php
namespace ThomasWoehlke\TwSimpleworklist\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Thomas Woehlke <woehlke@faktura-berlin.de>
 */
class TaskControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \ThomasWoehlke\TwSimpleworklist\Controller\TaskController
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = $this->getMock(\ThomasWoehlke\TwSimpleworklist\Controller\TaskController::class, ['redirect', 'forward', 'addFlashMessage'], [], '', false);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }



    /**
     * @test
     */
    public function showActionAssignsTheGivenTaskToView()
    {
        $task = new \ThomasWoehlke\TwSimpleworklist\Domain\Model\Task();

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('task', $task);

        $this->subject->showAction($task);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenTaskToView()
    {
        $task = new \ThomasWoehlke\TwSimpleworklist\Domain\Model\Task();

        $view = $this->getMock(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class);
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('task', $task);

        $this->subject->editAction($task);
    }


    /**
     * @test
     */
    public function updateActionUpdatesTheGivenTaskInTaskRepository()
    {
        $task = new \ThomasWoehlke\TwSimpleworklist\Domain\Model\Task();

        $taskRepository = $this->getMock(\ThomasWoehlke\TwSimpleworklist\Domain\Repository\TaskRepository::class, ['update'], [], '', false);
        $taskRepository->expects(self::once())->method('update')->with($task);
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $this->subject->updateAction($task);
    }
}
