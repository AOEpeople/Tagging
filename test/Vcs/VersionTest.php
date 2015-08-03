<?php
namespace AOE\Tagging\Tests\Vcs;

use AOE\Tagging\Vcs\Version;

/**
 * @package AOE\Tagging\Tests\Vcs
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldIncreaseVersionByTypePatch()
    {
        $version = new Version();
        $this->assertEquals('1.2.2', $version->increase('1.2.1', Version::INCREASE_PATCH));
    }

    /**
     * @test
     */
    public function shouldIncreaseVersionByTypeMinor()
    {
        $version = new Version();
        $this->assertEquals('1.3.0', $version->increase('1.2.2', Version::INCREASE_MINOR));
    }

    /**
     * @test
     */
    public function shouldIncreaseVersionByTypeMajor()
    {
        $version = new Version();
        $this->assertEquals('2.0.0', $version->increase('1.2.2', Version::INCREASE_MAJOR));
    }

    /**
     * @test
     */
    public function shouldIncreaseNullVersion()
    {
        $version = new Version();
        $this->assertEquals('0.0.1', $version->increase('0.0.0'));
    }
}
