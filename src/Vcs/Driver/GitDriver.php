<?php
namespace AOE\Tagging\Vcs\Driver;

use Webcreate\Vcs\Common\Adapter\CliAdapter;
use Webcreate\Vcs\Common\Reference;
use Webcreate\Vcs\Git;

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
     * Creates a Tag and push the specific tag into the remote.
     *
     * @param string $tag
     * @param string $path
     * @return void
     */
    public function tag($tag, $path)
    {
        $this->getGit()->getAdapter()->execute('tag', array($tag), $path);
        $this->getGit()->getAdapter()->execute('push', array('origin', 'tag', $tag), $path);
    }

    /**
     * Returns the latest tag from the given repository.
     * If no tag can be evaluated it will return "0.0.0".
     *
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
