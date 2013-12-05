<?php

namespace Mihaeu\Odin\Locator;

use Mihaeu\Odin\Resource\Resource;

class Locator
{
    /**
     * @var array
     */
    public $allowedExtensions;

    public function __construct(array $allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Recursively search a path for resources.
     *
     * @param string $path Path to the folder containing the resources.
     * @param        $type int Type of resources t be found at this path (Resource::TYPE_USER etc.)
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
