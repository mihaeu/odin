<?php

namespace Mihaeu\Odin\Writer;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Container\Container;
use Mihaeu\Odin\Configuration\ConfigurationInterface;

/**
 * Class Writer
 *
 * @package Mihaeu\Odin\Resource
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Writer
{
    /**
     * @var \Mihaeu\Odin\Configuration\ConfigurationInterface
     */
    private $config;

    /**
     * @var string
     */
    private $outputPath = null;

    /**
     * @var bool
     */
    private $assetsCopied = false;

    /**
     * @var bool
     */
    private $outputDirectoryClean = false;

    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    public function write(Resource $resource)
    {
        // clean output dir if necessary
        if ($this->outputDirectoryClean === false) {
            $this->cleanOutputDirectory();
        }

        // copy assets if necessary
        if ($this->assetsCopied === false) {
            $this->copyAssets();
        }

        // create folder structure and write content
        $this->createResourceFolderStructure($resource->meta['destination']);
        $bytesWritten = file_put_contents($resource->meta['destination'], $resource->content);
        return $bytesWritten !== false;
    }

    public function writeContainer(Container $container)
    {
        foreach ($container->getResources() as $resource) {
            $this->write($resource);
        }
    }

    public function createResourceFolderStructure($destination)
    {
        $tokens = explode('/', $destination);

        // if e.g. slug = blog/beginning/first-post.html
        // create folder blog and blog/beginning, otherwise, create nothing
        if (count($tokens) > 1) {
            $folderStructure = implode('/', array_splice($tokens, 0, -1));
            if (!file_exists($folderStructure)) {
                mkdir($folderStructure, 0777, true);
            }
        }
    }

    /**
     * Copies all assets from the theme folder to the output folder;
     *
     * @todo copy user assets, not just theme assets, don't copy template files
     */
    public function copyAssets()
    {
        $themeSubFolders = array_diff(scandir($this->config->get('theme')), ['.'], ['..']);
        foreach ($themeSubFolders as $file) {
            $folder = $this->config->get('theme').'/'.$file;
            if (is_dir($folder)) {
                $this->copyFolder($folder, $this->getOutputPath().'/'.$file);
            }
        }
        $this->assetsCopied = true;
    }

    /**
     * @param $path string
     *
     * @return bool
     */
    public function outputPathValid($path)
    {
        return is_dir($path) && is_writable($path);
    }

    /**
     * Finds the output path from the config.
     *
     * @todo absolute path cannot be determined
     *
     * @return string
     * @throws WriterException
     */
    public function getOutputPath()
    {
        if ($this->outputPath === null) {
            $this->outputPath = $this->config->get('output_folder');
        }
        return $this->outputPath;
    }

    public function cleanOutputDirectory()
    {
        $this->rrmdir($this->getOutputPath(), false);
        $this->outputDirectoryClean = true;
    }

    /**
     * Recursively delete everything inside a folder (including the folder).
     *
     * @param string $dir
     * @param bool   $removeRoot
     */
    public function rrmdir($dir, $removeRoot = false)
    {
        foreach (glob($dir.'/*') as $file) {
            if (is_dir($file)) {
                $this->rrmdir($file);
            } else {
                unlink($file);
            }
        }

        if ($removeRoot) {
            rmdir($dir);
        }
    }

    /**
     * @param $src string
     * @param $dst string
     */
    public function copyFolder($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.'/'.$file)) {
                    $this->copyFolder($src.'/'.$file, $dst.'/'.$file);
                } else {
                    copy($src.'/'.$file, $dst.'/'.$file);
                }
            }
        }
        closedir($dir);
    }
}
