<?php

namespace Mihaeu\Odin\Writer;

use Mihaeu\Odin\Resource\Resource;
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

        // determine destination, create folders, write content, ...
        $outputPath = $this->findResourceDestination($resource);
        $this->createResourceFolderStructure($outputPath);
        $bytesWritten = file_put_contents($outputPath, $resource->content);

        return $bytesWritten !== false;
    }

    public function writeAll(Array $resources)
    {
        foreach ($resources as $transformedResource) {
            $this->write($transformedResource);
        }
    }

    public function findResourceDestination(Resource &$resource)
    {
        // no slug defined, get one
        if (empty($resource->meta['slug'])) {
            $suffix = $this->config->get('pretty_urls') ? '/index.html' : '.html';
            $slug = $this->createSlug($resource).$suffix;
            $resource->meta['slug'] = $slug;
        } else {
            // local configuration always wins, so no changes if the slug
            // has been specified
            $slug = $resource->meta['slug'];
        }

        return $this->getOutputPath().'/'.$slug;
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
            // maybe the path in the config is absolute e.g. /tmp/myblog
            $absolutePath = $this->config->get('base_dir').'/'.$this->config->get('output_folder');

            // or maybe it is relative to the project root e.g. output
            $relativePath = $this->config->get('base_dir').'/'.$this->config->get('output_folder');

            if ($this->outputPathValid($absolutePath)) {
                $this->outputPath = $absolutePath;
            } elseif ($this->outputPathValid($relativePath)) {
                $this->outputPath = $relativePath;
            } else {
                // try creating the output folder
                $directoryCreated = mkdir($relativePath, 0777, true);

                if ($directoryCreated) {
                    $this->outputPath = $relativePath;
                } else {
                    throw new WriterException('Output path does not exist or is not writable or could not be created.');
                }
            }
        }
        return $this->outputPath;
    }

    /**
     * Copies all assets from the theme folder to the output folder;
     *
     * @todo copy user assets, not just theme assets, don't copy template files
     */
    public function copyAssets()
    {
        $themeFolder = $this->config->get('base_dir').'/'.$this->config->get('theme_folder').'/'.$this->config->get('theme');
        $themeSubFolders = array_diff(scandir($themeFolder), ['.'], ['..']);
        foreach ($themeSubFolders as $file) {
            $folder = $themeFolder.'/'.$file;
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
     * Create a slug using the pattern from the configuration
     *
     * @todo pattern! take date from file modification if not set
     *
     * @param string $title
     *
     * @return string
     */
    public function createSlug(Resource $resource)
    {
        // title not set
        if (empty($resource->meta['title'])) {
            $basename = $resource->file->getBasename($resource->file->getExtension());
            $resource->meta['title'] = $this->sluggify($basename);
        }

        // date not set
        if (empty($resource->meta['date'])) {
            $resource->meta['date'] = $resource->file->getMTime();
        }

        // preg split can remove beginning and trailing slashes
        $pattern = $this->config->get('permalink_pattern');
        $tokens = preg_split('/\//', $pattern, -1, PREG_SPLIT_NO_EMPTY);
        $slugTokens = [];
        $slugMatches = [
            ':title' => $this->sluggify($resource->meta['title']),
            ':Y'     => date('Y', $resource->meta['date']),
            ':y'     => date('y', $resource->meta['date']),
            ':m'     => date('m', $resource->meta['date']),
            ':d'     => date('d', $resource->meta['date'])
        ];
        foreach ($tokens as $token) {
            $slugTokens[] = isset($slugMatches[$token]) ? $slugMatches[$token] : $token;
        }

        return implode('/', $slugTokens);
    }

    /**
     * Create a slug for nicer URLs.
     *
     * @see http://htmlblog.net/seo-friendly-url-in-php/
     *
     * @param $string
     *
     * @return string
     */
    public function sluggify($string)
    {
        $string = preg_replace("`\[.*\]`U", "", $string);
        $string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace(
            "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i",
            "\\1",
            $string
        );
        $string = preg_replace(array("`[^a-z0-9]`i", "`[-]+`"), "-", $string);

        return strtolower(trim($string, '-'));
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
