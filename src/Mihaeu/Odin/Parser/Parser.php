<?php

namespace Mihaeu\Odin\Parser;

use dflydev\markdown\MarkdownExtraParser;
use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Container\Container;
use Mihaeu\Odin\Configuration\ConfigurationInterface;
use Mihaeu\Odin\Writer\WriterException;

/**
 * The resource parser will figure out what meta type a
 * resource has and parse them appropriately.
 *
 * @package Mihaeu\Odin\Resource
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Parser
{
    private $parserFactory;

    private $config;

    private $outputPath;

    public function __construct(ParserFactory $parserFactory, ConfigurationInterface $config)
    {
        $this->parserFactory = $parserFactory;
        $this->config = $config;
    }

    /**
     * Parses all resources.
     *
     * @param array $resources
     *
     * @return array
     */
    public function parseAll(Array $resources)
    {
        $parsedResources = [];
        foreach ($resources as $resource) {
            $parsedResources[] = $this->parse($resource);
        }
        return $parsedResources;
    }

    /**
     * Parse a resource, extracting meta information and content.
     *
     * @param Resource $resource
     */
    public function parse(Resource $resource)
    {
        // check what type of meta information and content the resource holds and fetch
        // the appropriate parser
        $metaParser = $this->parserFactory->getParser($resource);
        $resource->meta = $metaParser->parse($resource);
        $this->findResourceDestination($resource);
        return $resource;
    }

    public function parseContainer(Container &$container)
    {
        foreach ($container->getResources() as $resource) {
            $resource = $this->parse($resource);
            $container->setResource($resource->getId(), $resource);
        }
    }

    public function findResourceDestination(Resource &$resource)
    {
        // no slug defined, get one
        if (empty($resource->meta['slug'])) {
            $suffix = $this->config->get('pretty_urls') ? '/index.html' : '.html';
            $slug = $this->createSlug($resource).$suffix;
            $resource->meta['slug'] = $slug;
        }
        $destination = $this->getOutputPath().'/'.$resource->meta['slug'];
        $resource->meta['slug'] = str_replace('index.html', '', $resource->meta['slug']);
        $resource->meta['slug'] = rtrim($resource->meta['slug'], '/');
        $resource->meta['destination'] = $destination;

        return $destination;
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

    /**
     * @param $path string
     *
     * @return bool
     */
    public function outputPathValid($path)
    {
        return is_dir($path) && is_writable($path);
    }
}
