<?php

namespace Mihaeu\Odin\Writer;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Container\Container;
use Mihaeu\Odin\Configuration\ConfigurationInterface;
use Mihaeu\Odin\Processor\ContainerProcessorInterface;

/**
 * Class Writer
 *
 * @package Mihaeu\Odin\Resource
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Writer implements ContainerProcessorInterface
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

    public function process(Container &$container)
    {
        foreach ($container->getResources() as $resource) {
            $this->write($resource);
        }
    }

    public function write(Resource $resource)
    {
        // clean output dir if necessary
        if ($this->outputDirectoryClean === false) {
            $deletedFileCount = $this->cleanOutputDirectory();
            printf("Cleaned output directory (deleted $deletedFileCount files).\n");
        }

        // copy assets if necessary
        if ($this->assetsCopied === false) {
            $totalFilesizeCopied = $this->copyAssets();
            printf("Copied assets (%d kb).\n\n", $totalFilesizeCopied / 1024);
        }

        // create folder structure and write content
        $this->createResourceFolderStructure($resource->meta['destination']);
        $bytesWritten = file_put_contents($resource->meta['destination'], $resource->content);
        printf(
            "Wrote \033[01;31m%s\033[0m to %s\n",
            $resource->meta['title'],
            str_replace($this->config->get('base_dir'), '', $resource->meta['destination'])
        );
        return $bytesWritten !== false;
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
        $totalFilesizeCopied = 0;
        $themeSubFolders = array_diff(scandir($this->config->get('theme_folder')), ['.'], ['..']);
        foreach ($themeSubFolders as $file) {
            $folder = $this->config->get('theme_folder').'/'.$file;
            if (is_dir($folder)) {
                $totalFilesizeCopied += $this->copyFolder($folder, $this->getOutputPath().'/'.$file);
            }
        }
        $this->assetsCopied = true;
        return $totalFilesizeCopied;
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
        $deletedFileCount = $this->rrmdir($this->getOutputPath(), false);
        $this->outputDirectoryClean = true;
        return $deletedFileCount;
    }

    /**
     * Recursively delete everything inside a folder (including the folder).
     *
     * @param string $dir
     * @param bool   $removeRoot
     */
    public function rrmdir($dir, $removeRoot = false, $count = 0)
    {
        foreach (glob($dir.'/*') as $file) {
            if (is_dir($file)) {
                $count = $this->rrmdir($file, $removeRoot, $count);
            } else {
                ++$count;
                unlink($file);
            }
        }

        if ($removeRoot) {
            rmdir($dir);
        }

        return $count;
    }

    /**
     * @param $src string
     * @param $dst string
     */
    public function copyFolder($src, $dst, $totalFilesizeCopied = 0)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.'/'.$file)) {
                    $totalFilesizeCopied = $this->copyFolder($src.'/'.$file, $dst.'/'.$file, $totalFilesizeCopied);
                } else {
                    copy($src.'/'.$file, $dst.'/'.$file);
                    $totalFilesizeCopied += filesize($src.'/'.$file);
                }
            }
        }
        closedir($dir);
        return $totalFilesizeCopied;
    }
}
