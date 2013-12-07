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
        $userResources = array_filter(
            $this->resources,
            function (Resource $resource) {
                return $resource->type === Resource::TYPE_USER;
            }
        );
        $themeResources = array_filter(
            $this->resources,
            function (Resource $resource) {
                return $resource->type === Resource::TYPE_THEME;
            }
        );
        $systemResources = array_filter(
            $this->resources,
            function (Resource $resource) {
                return $resource->type === Resource::TYPE_SYSTEM;
            }
        );

        return [
            'site'             => $this->config->getAll(),
            'resources'        => $this->flattenResources($userResources),
            'theme_resources'  => $this->flattenResources($themeResources),
            'system_resources' => $this->flattenResources($systemResources),
            'all_tags'         => $this->getTags(),
            'all_categories'   => $this->getCategories()
        ];
    }

    public function flattenResource(Resource $resource)
    {
        return array_merge(
            $resource->meta,
            [
                'file'    => $resource->file,
                'type'    => $resource->type,
                'content' => $resource->content
            ]
        );
    }

    public function flattenResources(Array $resources)
    {
        $flatResources = [];
        foreach ($resources as $resource) {
            $flatResources[] = $this->flattenResource($resource);
        }
        return $flatResources;
    }

    public function getTags()
    {
        $tags = [];
        foreach ($this->resources as $resource) {
            $flatResource = $this->flattenResource($resource);
            if (isset($resource->meta['tags']) && is_array($resource->meta['tags'])) {
                // multiple tags
                foreach ($resource->meta['tags'] as $tag) {
                    $tags[$tag] = $flatResource;
                }
            } elseif (isset($resource->meta['tags'])) {
                // only one tag
                $tags[$resource->meta['tags']] = $flatResource;
            } elseif (isset($resource->meta['tag'])) {
                // only one tag
                $tags[$resource->meta['tag']] = $flatResource;
            }
        }
        return $tags;
    }

    public function getCategories()
    {
        $categories = [];
        foreach ($this->resources as $resource) {
            $flatResource = $this->flattenResource($resource);
            if (isset($resource->meta['categories']) && is_array($resource->meta['categories'])) {
                // multiple categories
                foreach ($resource->meta['categories'] as $category) {
                    $categories[$category] = $flatResource;
                }
            } elseif (isset($resource->meta['categories'])) {
                // only one category
                $categories[$resource->meta['categories']] = $flatResource;
            } elseif (isset($resource->meta['category'])) {
                // only one category
                $categories[$resource->meta['category']] = $flatResource;
            } else {
                // no category
                $categories['none'] = $flatResource;
            }
        }{}
        return $categories;
    }

    public function addResources(Array $resources)
    {
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }
}
