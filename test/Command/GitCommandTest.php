<?php
namespace AOE\Tagging\Tests\Command;

use AOE\Tagging\Command\GitCommand;
use AOE\Tagging\Tests\TaggingPHPUnitTestCase;
use AOE\Tagging\Vcs\Driver\GitDriver;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @package AOE\Tagging\Tests\Vcs
 */
class GitCommandTest extends TaggingPHPUnitTestCase
{
    /**
     * @test
     */
    public function execute()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'url' => 'git@git.test.test/foo/bar',
            'path' => '/home/foo/bar'
        ]);
    }

    /**
     * @test
     */
    public function executeVerbose()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar'
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );

        $this->assertRegExp('/Latest Tag number is "2.7.3"/', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function executeWithFromVersion()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);
        $gitDriver->expects($this->never())->method('getLatestTag');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--from-version' => '2.0.7'
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );

        $this->assertRegExp('/Next Tag number is "2.0.8"/', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function executeVerboseWithNoChanges()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(false);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar'
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );

        $this->assertRegExp(
            '/Skip creating tag "2.7.4" because there are no changes since tag "2.7.3"/',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function shouldCommitAdditionalFile()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag', 'commit'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitDriver->expects($this->once())->method('commit')->with(['myfile.ext'], '/home/foo/bar', '');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--commit-and-push' => ['myfile.ext'],
            ]
        );
    }

    /**
     * @test
     */
    public function shouldCommitAdditionalFiles()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag', 'commit'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitDriver->expects($this->once())->method('commit')->with(
            [
                'myfile.ext',
                'onemorefile.ext'
            ],
            '/home/foo/bar',
            ''
        );
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--commit-and-push' => ['myfile.ext', 'onemorefile.ext']
            ]
        );
    }

    /**
     * @test
     */
    public function shouldNotCommitAdditionalFilesIfNoChangesSinceLastTag()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag', 'commit'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(false);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitDriver->expects($this->never())->method('commit')->with('myfile.ext', '/home/foo/bar', '');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--commit-and-push' => ['myfile.ext'],
            ]
        );
    }

    /**
     * @test
     */
    public function shouldCommitAdditionalFileWithMessage()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag', 'commit'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitDriver->expects($this->once())->method('commit')
            ->with(['myfile.ext'], '/home/foo/bar', 'my message for commit');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--commit-and-push' => ['myfile.ext'],
                '--message' => 'my message for commit'
            ]
        );
    }

    /**
     * @test
     */
    public function shouldWriteCommitAndPushInfoWithVerbosityVerbose()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag', 'commit'])
            ->getMock();
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);
        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitDriver->expects($this->once())->method('commit')
            ->with(['myfile.ext'], '/home/foo/bar', 'my message for commit');
        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $test = $commandTester->getOutput();
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--commit-and-push' => ['myfile.ext'],
                '--message' => 'my message for commit'
            ],
            ['verbosity' => 2]
        );
    }

    /**
     * @test
     */
    public function shouldEvaluateVersionNumberIfChangesDetected()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag', 'commit'])
            ->getMock();

        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(true);

        $gitDriver->expects($this->never())->method('commit');
        $gitDriver->expects($this->never())->method('push');
        $gitDriver->expects($this->never())->method('tag');

        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--evaluate' => null
            ]
        );

        $this->assertRegExp(
            '~^2\.7\.4$~',
            $commandTester->getDisplay()
        );
    }

    /**
     * @test
     */
    public function shouldEvaluateVersionNumberIfNoChangesDetected()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestTag', 'tag', 'hasChangesSinceTag', 'commit'])
            ->getMock();

        $gitDriver->expects($this->once())->method('getLatestTag')->willReturn('2.7.3');
        $gitDriver->expects($this->once())->method('hasChangesSinceTag')->willReturn(false);

        $gitDriver->expects($this->never())->method('commit');
        $gitDriver->expects($this->never())->method('push');
        $gitDriver->expects($this->never())->method('tag');

        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--evaluate' => null
            ]
        );

        $this->assertEmpty($commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function shouldCheckoutBranchWithSwitchBranchOptionWhenExecuting()
    {
        $gitDriver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['checkoutBranch'])
            ->getMock();

        $gitDriver->expects($this->once())->method('checkoutBranch');

        $gitCommand = $this->getMockBuilder(GitCommand::class)
            ->setMethods(['getDriver'])
            ->getMock();
        $gitCommand->expects($this->once())->method('getDriver')->willReturn($gitDriver);

        /** @var GitCommand $gitCommand */
        $application = new Application();
        $application->add($gitCommand);

        $command = $application->find('git');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'url' => 'git@git.test.test/foo/bar',
                'path' => '/home/foo/bar',
                '--switch-branch' => null
            ]
        );
    }

    /**
     * @test
     */
    public function shouldGetDriver()
    {
        $gitCommand = new GitCommand();
        $driver = $this->invokeMethod($gitCommand, 'getDriver',['https://url']);
        $this->assertInstanceOf(GitDriver::class, $driver);
    }
}
