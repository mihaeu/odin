<?php

use Mihaeu\Odin\Locator\Locator;

use org\bovigo\vfs\vfsStream;

/**
 * Class LocatorTest.php
 *
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class LocatorTest extends PHPUnit_Framework_TestCase
{
    public function testTestPhpUnitSetup()
    {
        $mockConfig = \Mockery::mock('\Mihaeu\Odin\Configuration\YamlConfiguration');
        $mockConfig
            ->shouldReceive('get')
            ->with('resource_extensions')
            ->andReturn(['md', 'markdown', 'twig', 'html', 'xhtml', 'rst', 'txt', 'xml']);
        $locator = new Locator($mockConfig);
        $this->assertNotEmpty($locator);

        $this->assertEmpty($locator->locate('/doesnotexist'));

        $fileStructure = [
            'valid.md'             => 'f',
            'valid.twig'           => 'f',
            'badFormat.gif'        => 'f',
            'nested'               => 'd',
            'nested/valid.md'      => 'f',
            'nested/badFormat.jpg' => 'f'
        ];
        $testDir = $this->setUpTestDirectory($fileStructure);

        $this->assertCount(1, $locator->locate($testDir.'/nested'));
        $this->assertCount(3, $locator->locate($testDir));

        $this->tearDownTestDirectory($fileStructure, $testDir);
    }

    /**
     * Takes an array of files (in proper order) and creates readable dummy files
     * in the system tmp directory.
     *
     * @param array $fileStructure
     * @return string
     */
    public function setUpTestDirectory(array $fileStructure)
    {
        $testDir = sys_get_temp_dir().'/odintest'.microtime(true);
        mkdir($testDir);
        foreach ($fileStructure as $file => $type) {
            if ($type === 'f') {
                touch($testDir.DIRECTORY_SEPARATOR.$file);
            } else {
                mkdir($testDir.DIRECTORY_SEPARATOR.$file);
            }
        }
        return $testDir;
    }

    /**
     * Deletes all files from the array in the opposite (!) order.
     *
     * @param array $fileStructure
     * $param string $testDir Directory in which all the test files have been created.
     */
    public function tearDownTestDirectory(array $fileStructure, $testDir)
    {
        $reversedfileStructure = array_reverse($fileStructure, true);
        foreach ($reversedfileStructure as $file => $type) {
            if ($type === 'f') {
                unlink($testDir.DIRECTORY_SEPARATOR.$file);
            } else {
                rmdir($testDir.DIRECTORY_SEPARATOR.$file);
            }
        }
        rmdir($testDir);
    }
}
