<?php
namespace AOE\Tagging\Vcs\Driver;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package AOE\Tagging\Vcs\Driver
 */
interface DriverInterface
{
    /**
     * Creates a Tag and push the specific tag into the remote.
     *
     * @param string $tag
     * @param string $branch
     * @param string $path
     * @return void
     */
    public function tag($tag, $branch, $path);

    /**
     * Returns the latest tag from the given repository.
     * If no tag can be evaluated it will return "0.0.0".
     *
     * @return string
     */
    public function getLatestTag();

    /**
     * Returns true if the repository has changed since the last tag.
     *
     * @param string $tag
     * @param string $branch
     * @param string $path
     * @param OutputInterface $output
     * @return boolean
     */
    public function hasChangesSinceTag($tag, $branch, $path, OutputInterface $output);

    /**
     * Commits given files into the remote repository.
     *
     * @param array $files
     * @param string $path
     * @param string $message
     * @return void
     */
    public function commit(array $files, $path, $message = '');
}
