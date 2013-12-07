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
     * @var \Mihaeu\Odin\Configuration\ConfigurationInterface
     */
    private $config;

    /**
     * @var array
     */
    private $resources;

    /**
     * Constructor.
     */
    public function __construct(ConfigurationInterface $config)
    {
        $this->setConfig($config);
    }

    public function setConfig(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    public function addResource(Resource $resource)
    {
        if (empty($this->resources[$resource->getId()])) {
            $this->setResource($resource->getId(), $resource);
            return true;
        }
        return false;
    }

    public function setResource($id, Resource $resource)
    {
        $this->resources[$id] = $resource;
    }

    public function getResource($id)
    {
        if (empty($this->resources[$id])) {
            return false;
        }
        return $this->resources[$id];
    }

    public function removeResource($id)
    {
        if (empty($this->resources[$id])) {
            return false;
        }
        unset($this->resources[$id]);
        return true;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function getContainerArray()
    {
        $userResources = array_map(
            function (Resource $resource) {
                return $resource->type === Resource::TYPE_USER;
            },
            $this->resources
        );
        $themeResources = array_map(
            function (Resource $resource) {
                return $resource->type === Resource::TYPE_THEME;
            },
            $this->resources
        );
        $systemResources = array_map(
            function (Resource $resource) {
                return $resource->type === Resource::TYPE_SYSTEM;
            },
            $this->resources
        );

        return [
            'site'             => $this->config->getAll(),
            'resources'        => $this->flattenResources($userResources),
            'theme_resources'  => $this->flattenResources($themeResources),
            'system_resources' => $this->flattenResources($systemResources),
            'all_tags'         => $this->flattenResources($this->getTags()),
            'all_categories'   => $this->flattenResources($this->getCategories())
        ];
    }

    public function flattenResources(Array $resources)
    {
        return $resources;
    }

    public function getTags()
    {
        $tags = [];
        foreach ($this->resources as $resource) {
            if (isset($resource->meta['tags']) && is_array($resource->meta['tags'])) {
                // multiple tags
                foreach ($resource->meta['tags'] as $tag) {
                    $tags[$tag] = $resource;
                }
            } elseif (isset($resource->meta['tags'])) {
                // only one tag
                $tags[$resource->meta['tags']] = $resource;
            } elseif (isset($resource->meta['tag'])) {
                // only one tag
                $tags[$resource->meta['tag']] = $resource;
            }
        }
        return $tags;
    }

    public function getCategories()
    {
        $categories = [];
        foreach ($this->resources as $resource) {
            if (isset($resource->meta['categories']) && is_array($resource->meta['categories'])) {
                // multiple categories
                foreach ($resource->meta['categories'] as $category) {
                    $categories[$category] = $resource;
                }
            } elseif (isset($resource->meta['categories'])) {
                // only one category
                $categories[$resource->meta['categories']] = $resource;
            } elseif (isset($resource->meta['category'])) {
                // only one category
                $categories[$resource->meta['category']] = $resource;
            } else {
                // no category
                $categories['none'] = $resource;
            }
        }
        return $categories;
    }

    public function addResources(Array $resources)
    {
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }
}
