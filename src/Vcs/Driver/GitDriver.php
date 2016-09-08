<?php
namespace AOE\Tagging\Vcs\Driver;

use Webcreate\Vcs\Common\Adapter\CliAdapter;
use Webcreate\Vcs\Common\Reference;
use Webcreate\Vcs\Git;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrapper Class for Webcreate\Vcs\Git
 *
 * @package AOE\Tagging\Vcs\Driver
 */
class GitDriver implements DriverInterface
{
    /**
     * @var Git
     */
    private $git;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $executable;

    /**
     * @param string $url
     * @param string $executable
     */
    public function __construct($url, $executable = 'git')
    {
        $this->url = $url;
        $this->executable = $executable;
    }

    /**
     * @param string $tag
     * @param string $branch
     * @param string $path
     * @throws \Exception
     * @return void
     */
    public function tag($tag, $branch, $path)
    {
        try {
            $this->getGit()->getAdapter()->execute('tag', array($tag), $path);
            $this->getGit()->getAdapter()->execute('pull', ['origin', $branch], $path);
            $this->getGit()->getAdapter()->execute('push', ['origin', $branch], $path);
            $this->getGit()->getAdapter()->execute('push', array('origin', 'tag', $tag), $path);
        } catch (\Exception $e) {
            $this->getGit()->getAdapter()->execute('reset', array('--hard'), $path);
            $this->getGit()->getAdapter()->execute('tag', array('-d', $tag), $path);
            throw $e;
        }
    }

    /**
     * @param string $branch
     * @param string $path
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function checkoutBranch($branch, $path, OutputInterface $output)
    {
        try {
            $this->getGit()->getAdapter()->execute('checkout', ['-b', $branch ,'origin/' . $branch], $path);
            $output->writeln('<info>'.$branch.' erfolgreich ausgecheckt</info>');
        } catch (\Exception $e) {
            if (preg_match("/branch .+ already exists/", $e->getMessage()) === 1) {
                $output->writeln(
                    sprintf(
                        '<info>checkout -b %s %s failed, because local branch "%s" already exists</info>',
                        $branch,
                        'origin/' . $branch,
                        $branch
                    )
                );
                $this->getGit()->getAdapter()->execute('checkout', [$branch], $path);
                $output->writeln('<info>checking out local branch: "'. $branch .'"</info>');
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param array $files
     * @param string $path
     * @param string $message
     * @throws \Exception
     */
    public function commit(array $files, $path, $message = '')
    {
        try {
            $this->getGit()->getAdapter()->execute('add', $files, $path);
            $this->getGit()->getAdapter()->execute('commit', array_merge(array('-m', $message), $files), $path);
        } catch (\Exception $e) {
            if (false !== strpos($e->getMessage(), 'nothing to commit')) {
                return;
            }
            if (false !== strpos($e->getMessage(), 'nothing added to commit')) {
                return;
            }
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getLatestTag()
    {
        $tags = array();
        foreach ($this->getGit()->tags() as $reference) {
            /** @var Reference $reference */
            $tags[] = $reference->getName();
        }

        usort($tags, 'version_compare');

        if (empty($tags)) {
            return '0.0.0';
        }

        return call_user_func('end', array_values($tags));
    }

    /**
     * @param string $tag
     * @param string $branch
     * @param string $path
     * @param OutputInterface $output
     * @return boolean
     */
    public function hasChangesSinceTag($tag, $branch, $path, OutputInterface $output)
    {
        try {
            $this->getGit()->getAdapter()->execute('fetch', ['origin'], $path);
            $diff = $this->getGit()->getAdapter()->execute('diff', array('--ignore-all-space', $tag, $branch), $path);
        } catch (\RuntimeException $e) {
            if (false !== strpos($e->getMessage(), 'unknown revision or path')) {
                return true;
            }
            throw $e;
        }

        if (null === $diff || "" === $diff) {
            return false;
        }

        return true;
    }

    /**
     * @return Git
     */
    protected function getGit()
    {
        if (null === $this->git) {
            $this->git = new Git($this->url);
            /** @var CliAdapter $adapter */
            $adapter = $this->git->getAdapter();
            $adapter->setExecutable($this->executable);
        }

        return $this->git;
    }
}
