<?php
namespace AOE\Tagging\Vcs\Driver;

/**
 * @package AOE\Tagging\Vcs\Driver
 */
class SvnDriver implements DriverInterface
{
    /**
     * @param string $tag
     * @param string $path
     * @param string $branch
     * @return void
     */
    public function tag($tag, $path, $branch)
    {
        // TODO: Implement tag() method.
    }

    /**
     * @return string
     */
    public function getLatestTag()
    {
        // TODO: Implement getLatestTag() method.
    }

    /**
     * @param string $tag
     * @param string $path
     * @return boolean
     */
    public function hasChangesSinceTag($tag, $path)
    {
        // TODO: Implement hasChangesSinceTag() method.
    }

    /**
     * @param array $files
     * @param string $path
     * @param string $message
     * @return void
     */
    public function commit(array $files, $path, $message = '')
    {
        // TODO: Implement commit() method.
    }
}
