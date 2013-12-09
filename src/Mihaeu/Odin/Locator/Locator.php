<?php

namespace Mihaeu\Odin\Locator;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Configuration\ConfigurationInterface;

/**
 * Class Locator
 *
 * Locates all resources (user, theme or system) and classifies them as such.
 *
 * @package Mihaeu\Odin\Locator
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Locator
{
    /**
     * @var array
     */
    public $allowedExtensions;

    /**
     * Constructor.
     *
     * @param ConfigurationInterface $config
     */
    public function __construct(ConfigurationInterface $config)
    {
        $this->allowedExtensions = $config->get('resource_extensions');
    }

    /**
     * Recursively search a path for resources.
     *
     * @param string $path  Path to the folder containing the resources.
     * @param int    $type  Type of resources t be found at this path (Resource::TYPE_USER etc.)
     *
     * @return array Resources
     */
    public function locate($path, $type)
    {
        if (!file_exists($path) || !is_dir($path)) {
            return [];
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        $resources = [];
        foreach ($files as $file) {
            // ignore linux . and ..
            // and filter resource files
            if (strpos($file->getFilename(), '.') !== 0
                && in_array($file->getExtension(), $this->allowedExtensions)
            ) {
                $resources[] = new Resource($file, $type);
            }
        }
        return $resources;
    }
}
