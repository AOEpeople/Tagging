<?php
namespace AOE\Tagging\Vcs\Driver;

/**
 * @package AOE\Tagging\Vcs\Driver
 */
class SvnDriver implements DriverInterface
{
    /**
     * Creates a Tag and push the specific tag into the remote.
     *
     * @param string $tag
     * @param string $path
     * @return void
     */
    public function tag($tag, $path)
    {
        // TODO: Implement tag() method.
    }

    /**
     * Returns the latest tag from the given repository.
     * If no tag can be evaluated it will return "0.0.0".
     *
     * @return string
     */
    public function getLatestTag()
    {
        // TODO: Implement getLatestTag() method.
    }
}
