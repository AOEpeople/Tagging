<?php
namespace AOE\Tagging\Tests\Vcs\Driver;

use AOE\Tagging\Vcs\Driver\GitDriver;

/**
 * @package AOE\Tagging\Tests\Vcs
 */
class GitDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldTagAndPush()
    {
        $driver = new GitDriver('git@git.test.test/foo/bar');
    }

    /**
     * @test
     */
    public function shouldGetLatestVersion()
    {
        $driver = new GitDriver('git@git.test.test/foo/bar');
    }
}