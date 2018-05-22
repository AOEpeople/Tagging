<?php
namespace AOE\Tagging\Tests\Vcs\Driver;

use AOE\Tagging\Tests\TaggingPHPUnitTestCase;
use AOE\Tagging\Vcs\Driver\GitDriver;
use Symfony\Component\Console\Output\OutputInterface;
use Webcreate\Vcs\Common\Reference;
use Webcreate\Vcs\Git;

/**
 * @package AOE\Tagging\Tests\Vcs
 */
class GitDriverTest extends TaggingPHPUnitTestCase
{
    /**
     * @test
     */
    public function shouldCreateGitDriver()
    {
        $this->assertInstanceOf(GitDriver::class, new GitDriver('https://test', 'git'));
    }


    /**
     * @test
     */
    public function shouldTagAndPush()
    {
        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'tag',
            ['0.2.5'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'pull',
            ['--rebase', 'origin', 'feature/myBranch'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(2))->method('execute')->with(
            'push',
            ['origin', 'feature/myBranch'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(3))->method('execute')->with(
            'push',
            ['origin', 'tag', '0.2.5'],
            '/home/my/vcs/repo'
        );

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(4))->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $driver->tag('0.2.5', 'feature/myBranch', '/home/my/vcs/repo');
    }

    /**
     * test
     * expectedException \Exception
     */
    public function shouldCleanOnError()
    {
        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'tag',
            ['0.2.5'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'pull',
            ['--rebase', 'origin', 'master'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(2))->method('execute')->with(
            'push',
            ['origin', 'master'],
            '/home/my/vcs/repo'
        )->willThrowException(new \Exception('could not push to remote'));

        $adapter->expects($this->at(3))->method('execute')->with(
            'reset',
            ['--hard'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(4))->method('execute')->with(
            'tag',
            ['-d', '0.2.5'],
            '/home/my/vcs/repo'
        );

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->exactly(5))->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(5))->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $driver->tag('0.2.5', 'master', '/home/my/vcs/repo');
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function shouldAbortRebaseOnPullError()
    {
        $this->markTestSkipped('currently rebase abort is disabled');

        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'tag',
            ['0.2.5'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'pull',
            [],
            '/home/my/vcs/repo'
        )->willThrowException(new \Exception('could not push to remote'));

        $adapter->expects($this->at(2))->method('execute')->with(
            'rebase',
            ['--abort'],
            '/home/my/vcs/repo'
        );

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->exactly(5))->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(5))->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $driver->tag('0.2.5', '/home/my/vcs/repo');
    }

    /**
     * @test
     */
    public function shouldNotHaveChangesSinceTagWithNullValueFromGitAdapter()
    {
        $adapter = $this->givenAnAdapter();
        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $adapter->expects($this->at(0))->method('execute')->with(
            'fetch',
            ['origin'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'diff',
            ['--ignore-all-space', '0.2.5', 'master'],
            '/home/my/vcs/repo'
        )->willReturn(null);

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->exactly(2))->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->any())->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $this->assertFalse($driver->hasChangesSinceTag('0.2.5', 'master', '/home/my/vcs/repo', $output));
    }

    /**
     * @test
     */
    public function shouldNotHaveChangesSinceTagWithEmptyStringValueFromGitAdapter()
    {
        $adapter = $this->givenAnAdapter();
        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $adapter->expects($this->at(0))->method('execute')->with(
            'fetch',
            ['origin'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'diff',
            ['--ignore-all-space', '0.2.5', 'master'],
            '/home/my/vcs/repo'
        )->willReturn("");

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $this->assertFalse($driver->hasChangesSinceTag('0.2.5', 'master', '/home/my/vcs/repo', $output));
    }


    /**
     * @test
     */
    public function shouldHaveChangesSinceTag()
    {
        $adapter = $this->givenAnAdapter();
        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $adapter->expects($this->at(0))->method('execute')->with(
            'fetch',
            ['origin'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'diff',
            ['--ignore-all-space', '0.2.5', 'master'],
            '/home/my/vcs/repo'
        )->willReturn('diff --git a/TEST b/TEST
index 56a6051..d2b3621 100644
--- a/TEST
+++ b/TEST
@@ -1 +1 @@
-1
\ No newline at end of file
+1asdf
\ No newline at end of file');

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->exactly(2))->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->any())->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $this->assertTrue($driver->hasChangesSinceTag('0.2.5', 'master', '/home/my/vcs/repo', $output));
    }

    /**
     * @test
     */
    public function shouldHaveChangesSinceTagOnUnknownTag()
    {
        $adapter = $this->givenAnAdapter();
        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $adapter->expects($this->at(0))->method('execute')->with(
            'fetch',
            ['origin'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'diff',
            ['--ignore-all-space', '0.2.5', 'master'],
            '/home/my/vcs/repo'
        )->willThrowException(
            new \RuntimeException('ambiguous argument \'0.0.0\': unknown revision or path not in the working tree.')
        );

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $this->assertTrue($driver->hasChangesSinceTag('0.2.5', 'master', '/home/my/vcs/repo', $output));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function shouldThrowRuntimeExceptionOnDiffFailureHavingChangesSinceTag()
    {
        $adapter = $this->givenAnAdapter();
        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new \RuntimeException('some error message');

        $adapter->expects($this->at(0))->method('execute')->with(
            'fetch',
            ['origin'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'diff',
            ['--ignore-all-space', '0.2.5', 'master'],
            '/home/my/vcs/repo'
        )->willThrowException($exception);

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $this->assertTrue($driver->hasChangesSinceTag('0.2.5', 'master', '/home/my/vcs/repo', $output));
    }

    /**
     * @test
     * @param array $references
     * @dataProvider references
     */
    public function shouldGetLatestVersion(array $references)
    {
        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['tags'])
            ->getMock();
        $git->expects($this->once())->method('tags')->willReturn($references);

        $driver = $this->givenADriver();

        $driver->expects($this->once())->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $this->assertEquals('0.12.0', $driver->getLatestTag());
    }

    /**
     * @test
     */
    public function shouldGetTag0_0_0WhenNoTagsExists()
    {
        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['tags'])
            ->getMock();
        $git->expects($this->once())->method('tags')->willReturn([]);

        $driver = $this->givenADriver();

        $driver->expects($this->once())->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $this->assertEquals('0.0.0', $driver->getLatestTag());
    }

    /**
     * @test
     */
    public function shouldCommitFile()
    {
        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'add',
            ['myfile.ext'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'commit',
            ['-m', 'my message', 'myfile.ext'],
            '/home/my/vcs/repo'
        );

        $git = $this->givenAGitClient($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        /** @var GitDriver $driver */
        $driver->commit(['myfile.ext'], '/home/my/vcs/repo', 'my message');
    }

    /**
     * @test
     */
    public function shouldIgnoreExceptionNothingToCommit()
    {
        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'add',
            ['myfile.ext'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'commit',
            ['-m', 'my message', 'myfile.ext'],
            '/home/my/vcs/repo'
        )->willThrowException(new \Exception('nothing to commit (working directory clean)'));

        $git = $this->givenAGitClient($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        $driver->commit(['myfile.ext'], '/home/my/vcs/repo', 'my message');
    }

    /**
     * @test
     */
    public function shouldIgnoreExceptionNothingAddedToCommit()
    {
        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'add',
            ['myfile.ext'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'commit',
            ['-m', 'my message', 'myfile.ext'],
            '/home/my/vcs/repo'
        )->willThrowException(
            new \Exception(
                'nothing added to commit but untracked files present (use "git add" to track)'
            )
        );

        $git = $this->givenAGitClient($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        $driver->commit(['myfile.ext'], '/home/my/vcs/repo', 'my message');
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function shouldThrowException()
    {
        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'add',
            ['myfile.ext'],
            '/home/my/vcs/repo'
        );

        $adapter->expects($this->at(1))->method('execute')->with(
            'commit',
            ['-m', 'my message', 'myfile.ext'],
            '/home/my/vcs/repo'
        )->willThrowException(new \Exception('Some other error'));

        $git = $this->givenAGitClient($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        $driver->commit(['myfile.ext'], '/home/my/vcs/repo', 'my message');
    }

    /**
     * @test
     */
    public function shouldCheckoutBranch()
    {
        $adapter = $this->givenAnAdapter();
        $adapter->expects($this->at(0))->method('execute')->with(
            'checkout',
            ['-b', 'bugfix/branch', 'origin/bugfix/branch'],
            '/home/my/vcs/repo'
        );

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);

        $git = $this->givenAGitClient($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(1))->method('getGit')->willReturn($git);

        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $driver->checkoutBranch('bugfix/branch', '/home/my/vcs/repo', $output);
    }

    /**
     * @test
     */
    public function shouldCheckoutLocalBranch()
    {
        $adapter = $this->givenAnAdapter();

        $adapter->expects($this->at(0))->method('execute')->with(
            'checkout',
            ['-b', 'bugfix/branch', 'origin/bugfix/branch'],
            '/home/my/vcs/repo'
        )->willThrowException(new \Exception("branch bugfix/branch already exists"));

        $adapter->expects($this->at(1))->method('execute')->with(
            'checkout',
            ['bugfix/branch'],
            '/home/my/vcs/repo'
        );

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);

        $git = $this->givenAGitClient($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(2))->method('getGit')->willReturn($git);

        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $driver->checkoutBranch('bugfix/branch', '/home/my/vcs/repo', $output);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function shouldThrowExceptionWhileCheckingOutLocalBranch()
    {
        $adapter = $this->givenAnAdapter();

        $exception = new \Exception("some exception");

        $adapter->expects($this->at(0))->method('execute')->with(
            'checkout',
            ['-b', 'bugfix/branch', 'origin/bugfix/branch'],
            '/home/my/vcs/repo'
        )->willThrowException($exception);

        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);

        $git = $this->givenAGitClient($adapter);

        $driver = $this->givenADriver();

        $driver->expects($this->exactly(1))->method('getGit')->willReturn($git);

        $output = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $driver->checkoutBranch('bugfix/branch', '/home/my/vcs/repo', $output);
    }

    /**
     * @test
     */
    public function shouldGetGit()
    {
        $driver = new GitDriver('https://url', 'git');
        $this->invokeMethod($driver, 'getGit');
    }

    /**
     * @return array
     */
    public function references()
    {
        return [
            'normal order' => [
                [
                    new Reference('0.1.0'),
                    new Reference('0.2.0'),
                    new Reference('0.3.0'),
                    new Reference('0.4.0'),
                    new Reference('0.5.0'),
                    new Reference('0.6.0'),
                    new Reference('0.7.0'),
                    new Reference('0.8.0'),
                    new Reference('0.9.0'),
                    new Reference('0.10.0'),
                    new Reference('0.11.0'),
                    new Reference('0.12.0')
                ]
            ],
            'weird order' => [
                [
                    new Reference('0.10.0'),
                    new Reference('0.11.0'),
                    new Reference('0.12.0'),
                    new Reference('0.1.0'),
                    new Reference('0.2.0'),
                    new Reference('0.3.0'),
                    new Reference('0.4.0'),
                    new Reference('0.5.0'),
                    new Reference('0.6.0'),
                    new Reference('0.7.0'),
                    new Reference('0.8.0'),
                    new Reference('0.9.0')
                ]
            ]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenAnAdapter()
    {
        $adapter = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['tag', 'push', 'execute', 'setClient'])
            ->getMock();
        return $adapter;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GitDriver
     */
    private function givenADriver()
    {
        $driver = $this->getMockBuilder(GitDriver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGit'])
            ->getMock();
        return $driver;
    }

    /**
     * @param $adapter
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenAGitClient($adapter)
    {
        $git = $this->getMockBuilder(Git::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAdapter'])
            ->getMock();
        $git->expects($this->any())->method('getAdapter')->willReturn($adapter);
        return $git;
    }
}
