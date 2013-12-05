<?php

namespace Mihaeu\Odin\Container;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Configuration\ConfigurationInterface;

/**
 * Class Container
 * @package Mihaeu\Odin\Container
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Container
{
    /**
     * @var array
     */
    private $container;

    /**
     * @var \Mihaeu\Odin\Configuration\ConfigurationInterface
     */
    private $config;

    /**
     * Constructor.
     */
    public function __construct(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    public function getContainerArray(Array $resources)
    {
        $this->container = ['site' => $this->config->getAll()];

        // fill container with resources
        foreach ($resources as $resource) {
            $id = $resource->file->getRealPath();
            $metaAndContent = array_merge(
                $resource->meta,
                [
                    'content' => $resource->content,
                    'type'    => $resource->type,
                    'file'    => $resource->file
                ]
            );

            // separate resources by type
            if ($resource->type === Resource::TYPE_USER) {
                $this->container['resources'][$id] = $metaAndContent;
            } else {
                if ($resource->type === Resource::TYPE_THEME) {
                    $this->container['resources_theme'][$id] = $metaAndContent;
                } else {
                    $this->container['resources_system'][$id] = $metaAndContent;
                }
            }

            // filter categories
            if (isset($resource->meta['categories']) && is_array($resource->meta['categories'])) {
                // multiple categories
                foreach ($resource->meta['categories'] as $category) {
                    $this->container['categories'][$category][$id] = $metaAndContent;
                }
            } else {
                if (isset($resource->meta['categories'])) {
                    // only one category
                    $this->container['categories'][$resource->meta['categories']][$id] = $metaAndContent;
                } else {
                    if (isset($resource->meta['category'])) {
                        // only one category
                        $this->container['categories'][$resource->meta['category']][$id] = $metaAndContent;
                    } else {
                        // no category
                        $this->container['categories']['none'][$id] = $metaAndContent;
                    }
                }
            }

            // filter tags
            if (isset($resource->meta['tags']) && is_array($resource->meta['tags'])) {
                // multiple categories
                foreach ($resource->meta['tags'] as $tag) {
                    $this->container['tags'][$tag][$id] = $metaAndContent;
                }
            } else {
                if (isset($resource->meta['tags'])) {
                    // only one category
                    $this->container['tags'][$resource->meta['tags']][$id] = $metaAndContent;
                } else {
                    if (isset($resource->meta['tag'])) {
                        // only one category
                        $this->container['tags'][$resource->meta['tag']][$id] = $metaAndContent;
                    }
                }
            }
        }

        return $this->container;
    }
}
