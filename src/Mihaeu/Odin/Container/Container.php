<?php

namespace Mihaeu\Odin\Container;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Configuration\ConfigurationInterface;

/**
 * Container
 *
 * The container holds the configuration and all the resources. All processes operate on the container and
 * its contents.
 *
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
     * @var array
     */
    private $categories;

    /**
     * @var array
     */
    private $tags;

    /**
     * Constructor.
     */
    public function __construct(ConfigurationInterface $config)
    {
        $this->setConfig($config);
    }

    /**
     * Updates the configuration.
     *
     * @todo Configuration items should either be changed directly or through the container, but not through both.
     *
     * @param ConfigurationInterface $config
     */
    public function setConfig(ConfigurationInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Adds a resource to the container, assuming it didn't exist before.
     *
     * @param Resource $resource
     *
     * @return bool
     */
    public function addResource(Resource $resource)
    {
        if (empty($this->resources[$resource->getId()])) {
            $this->setResource($resource->getId(), $resource);
            return true;
        }
        return false;
    }

    /**
     * Adds an array of resources to the container.
     *
     * @param array $resources
     *
     * @return void
     */
    public function addResources(Array $resources)
    {
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    /**
     * Adds a resource to the container or updates and existing one.
     *
     * @param string   $id md5 hashed id using content and uri (see Resource class)
     * @param Resource $resource
     *
     * @return void
     */
    public function setResource($id, Resource $resource)
    {
        $this->resources[$id] = $resource;
    }

    /**
     * Returns a resource from the container.
     *
     * @param string $id md5 hashed id using content and uri (see Resource class)
     *
     * @return bool
     */
    public function getResource($id)
    {
        if (empty($this->resources[$id])) {
            return false;
        }
        return $this->resources[$id];
    }

    /**
     * Removes a resource from the container.
     *
     * @param string $id md5 hashed id using content and uri (see Resource class)
     *
     * @return bool
     */
    public function removeResource($id)
    {
        if (empty($this->resources[$id])) {
            return false;
        }
        unset($this->resources[$id]);
        return true;
    }

    /**
     * Returns all resources.
     *
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Returns the flattened contents of the container for easier processing (e.g. templating).
     *
     * @return array
     */
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

    /**
     * Strips down and flattens a resource into an array.
     *
     * @param Resource $resource
     *
     * @return array
     */
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

    /**
     * Flattens all resources.
     *
     * @see flattenResource()
     *
     * @param array $resources
     *
     * @return array
     */
    public function flattenResources(Array $resources)
    {
        $flatResources = [];
        foreach ($resources as $resource) {
            $flattenedResource = $this->flattenResource($resource);
            $key = $flattenedResource['date'].'-'.$flattenedResource['title'];
            $flatResources[$key] = $flattenedResource;
        }
        return $flatResources;
    }

    /**
     * Filters all resources for tags and returns a flattened array of the tags and their resources.
     *
     * @todo Extract filter class and add this functionality there.
     *
     * @return array
     */
    public function getTags()
    {
        if ($this->tags) {
            return $this->tags;
        }

        $this->tags = [];
        foreach ($this->resources as $resource) {
            $flatResource = $this->flattenResource($resource);
            $key = $flatResource['date'].'-'.$flatResource['title'];
            if (isset($resource->meta['tags']) && is_array($resource->meta['tags'])) {
                // multiple tags
                foreach ($resource->meta['tags'] as $tag) {
                    $this->tags[$tag][$key] = $flatResource;
                }
            } elseif (isset($resource->meta['tags'])) {
                // only one tag
                $this->tags[$resource->meta['tags']][$key] = $flatResource;
            } elseif (isset($resource->meta['tag'])) {
                // only one tag
                $this->tags[$resource->meta['tag']][$key] = $flatResource;
            }
        }
        return $this->tags;
    }

    /**
     * Filters all resources for categories and returns a flattened array of the categories and their resources.
     *
     * @todo Extract filter class and add this functionality there.
     *
     * @return array
     */
    public function getCategories()
    {
        if ($this->categories) {
            return $this->categories;
        }

        $this->categories = [];
        foreach ($this->resources as $resource) {
            $flatResource = $this->flattenResource($resource);
            $key = $flatResource['date'].'-'.$flatResource['title'];
            if (isset($resource->meta['categories']) && is_array($resource->meta['categories'])) {
                // multiple categories
                foreach ($resource->meta['categories'] as $category) {
                    $this->categories[$category][$key] = $flatResource;
                }
            } elseif (isset($resource->meta['categories'])) {
                // only one category
                $this->categories[$resource->meta['categories']][$key] = $flatResource;
            } elseif (isset($resource->meta['category'])) {
                // only one category
                $this->categories[$resource->meta['category']][$key] = $flatResource;
            } else {
                // no category
                $this->categories['none'][$key] = $flatResource;
            }
        }
        return $this->categories;
    }

    /**
     * Generates resources from categories.
     * 
     * @return void
     */
    public function generateCategories()
    {
        foreach ($this->getCategories() as $name => $category) {
            $fakeFile = new \SplFileInfo(tempnam(sys_get_temp_dir(), 'moab'));
            $categoryResource = new Resource($fakeFile);
            $categoryResource->meta = [
                'title'         => $name,
                'slug'          => 'category/'.strtolower($name),
                'date'          => time(),
                'destination'   => $this->config->get('output_folder').'/category/'.strtolower($name).'/index.html',
                'layout'        => '@theme/layouts/categories.html.twig'
            ];
            $this->setResource(md5($name), $categoryResource);
        }
    }

    /**
     * Generates resources from tags.
     * 
     * @return void
     */
    public function generateTags()
    {
        foreach ($this->getTags() as $name => $tag) {
            $fakeFile = new \SplFileInfo(tempnam(sys_get_temp_dir(), 'moab'));
            $tagResource = new Resource($fakeFile);
            $tagResource->meta = [
                'title'         => $name,
                'slug'          => 'tag/'.strtolower($name),
                'date'          => time(),
                'destination'   => $this->config->get('output_folder').'/tag/'.strtolower($name).'/index.html',
                'layout'        => '@theme/layouts/tags.html.twig'
            ];
            $this->setResource(md5($name), $tagResource);
        }
    }
}
