<?php
namespace AOE\Tagging\Tests\Command;

use AOE\Tagging\Command\GitCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package AOE\Tagging\Tests\Vcs
 */
class GitCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function execute()
    {
        $gitDriver = $this->getMockBuilder('AOE\\Tagging\\Vcs\\Driver\\GitDriver')
            ->disableOriginalConstructor()
            ->setMethods(array('getLatestTag', 'tag'))
            ->getMock();
        $gitDriver->expects($this->once())->method('getLatestTag')->will($this->returnValue('2.7.3'));
        $gitCommand = $this->getMockBuilder('AOE\\Tagging\\Command\\GitCommand')
            ->setMethods(array('getDriver'))
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->will($this->returnValue($gitDriver));

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'url' => 'git@git.test.test/foo/bar',
            'path' => '/home/foo/bar'
        ));
    }

    /**
     * @test
     */
    public function executeVerbose()
    {
        $gitDriver = $this->getMockBuilder('AOE\\Tagging\\Vcs\\Driver\\GitDriver')
            ->disableOriginalConstructor()
            ->setMethods(array('getLatestTag', 'tag'))
            ->getMock();
        $gitDriver->expects($this->once())->method('getLatestTag')->will($this->returnValue('2.7.3'));
        $gitCommand = $this->getMockBuilder('AOE\\Tagging\\Command\\GitCommand')
            ->setMethods(array('getDriver'))
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->will($this->returnValue($gitDriver));

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar'
            ),
            array(
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            )
        );

        $this->assertRegExp('/Latest Tag number is "2.7.3"/', $commandTester->getDisplay());
    }
}
