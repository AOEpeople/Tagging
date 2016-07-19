<?php
namespace AOE\Tagging\Vcs\Driver;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package AOE\Tagging\Vcs\Driver
 */
class SvnDriver implements DriverInterface
{
    /**
     * @param string $tag
     * @param string $branch
     * @param string $path
     * @return void
     */
    public function tag($tag, $branch, $path)
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
     * @param string $branch
     * @param string $path
     * @param OutputInterface $output
     * @return boolean
     */
    public function hasChangesSinceTag($tag, $branch, $path, OutputInterface $output)
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
