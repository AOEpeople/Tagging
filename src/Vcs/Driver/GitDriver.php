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
     * @var CliAdapter
     */
    private $adapter;

    /**
     * @param string $url
     * @param string $executable
     */
    public function __construct($url, $executable = 'git')
    {
        $this->git = new Git($url);
        $this->adapter = $this->git->getAdapter();
        if (null !== $executable) {
            $this->adapter->setExecutable($executable);
        }
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
        $this->git->getAdapter()->execute('tag', array($tag), $path);
        $this->git->getAdapter()->execute('push', array('origin', 'tag', $tag), $path);
    }

    /**
     * Returns the latest tag from the given repository.
     * If no tag can be evaluated it will return "0.0.0".
     *
     * @return string
     */
    public function getLatestTag()
    {
        $reference = call_user_func('end', array_values($this->git->tags()));
        if ($reference instanceof Reference) {
            return $reference->getName();
        }
        return '0.0.0';
    }
}
