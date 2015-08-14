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
     * @return void
     */
    public function tag($tag, $path)
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
     * @param string $file
     * @param string $path
     * @param string $message
     * @return void
     */
    public function commit($file, $path, $message = '')
    {
        // TODO: Implement commit() method.
    }
}
