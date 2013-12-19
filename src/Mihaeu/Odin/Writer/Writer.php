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

    /**
     * @var array
     */
    private $info = [];

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
            $this->info['output_cleaned'] = sprintf("Cleaned output directory (deleted $deletedFileCount files).");
        }

        // copy assets if necessary
        if ($this->assetsCopied === false) {
            $totalFilesizeCopied = $this->copyAssets();
            $this->info['assets_copied'] = sprintf("Copied assets (~%d kb).", ceil($totalFilesizeCopied / 1024));
        }

        // create folder structure and write content
        $this->createResourceFolderStructure($resource->meta['destination']);
        $bytesWritten = file_put_contents($resource->meta['destination'], $resource->content);
        $this->info['resources_written'][] = sprintf(
            "Wrote \033[01;31m%s\033[0m to %s",
            $resource->meta['title'],
            str_replace($this->config->get('base_dir'), '', $resource->meta['destination'])
        );
        return $bytesWritten !== false;
    }

    public function createResourceFolderStructure($destination)
    {
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }
    }

    /**
     * Copies all assets from the theme folder to the output folder;
     *
     * @todo copy user assets, not just theme assets, don't copy template files
     */
    public function copyAssets()
    {
        // filter out ignored assets like bower and ignore linux . and ..
        $themeAssetPath = $this->config->get('theme_assets');
        if (!is_dir($themeAssetPath)) {
            $this->assetsCopied = true;
            return 0;
        }

        $themeAssets = array_diff(
            scandir($themeAssetPath),
            $this->config->get('ignore_assets'),
            ['.', '..']
        );

        $totalFilesizeCopied = 0;
        foreach ($themeAssets as $asset) {
            $totalFilesizeCopied += $this->copyFolder(
                $themeAssetPath.DIRECTORY_SEPARATOR.$asset,
                $this->getOutputPath().DIRECTORY_SEPARATOR.basename($themeAssetPath).DIRECTORY_SEPARATOR.$asset
            );
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
    public function rrmdir($dir, $removeRoot = false)
    {
        $count = 0;
        foreach (array_diff(scandir($dir), ['.', '..']) as $file) {
            if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
                $count += $this->rrmdir($dir.DIRECTORY_SEPARATOR.$file, $removeRoot);
            } else {
                ++$count;
                unlink($dir.DIRECTORY_SEPARATOR.$file);
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
    public function copyFolder($src, $dst)
    {
        if (is_file($src)) {
            if (!is_dir(dirname($dst))) {
                mkdir(dirname($dst));
            }

            copy($src, $dst);
            return filesize($src);
        }

        $totalFilesizeCopied = 0;
        $dir = opendir($src);
        @mkdir($dst, 0777, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.DIRECTORY_SEPARATOR.$file)) {
                    $totalFilesizeCopied += $this->copyFolder($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file, $totalFilesizeCopied);
                } else {
                    copy($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file);
                    $totalFilesizeCopied += filesize($src.DIRECTORY_SEPARATOR.$file);
                }
            }
        }
        closedir($dir);
        return $totalFilesizeCopied;
    }

    /**
     * Returns information about the writing process.
     *
     * @param string $key
     *
     * @return array
     */
    public function getInfo($key = '')
    {
        // no key specified
        if (empty($key)) {
            return $this->info;
        }

        // key legal
        if (isset($this->info[$key])) {
            return $this->info[$key];
        }

        // no info
        return '';
    }
}
