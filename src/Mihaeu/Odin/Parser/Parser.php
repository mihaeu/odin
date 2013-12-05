<?php

namespace Mihaeu\Odin\Parser;

use dflydev\markdown\MarkdownExtraParser;
use Mihaeu\Odin\Resource\Resource;

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

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
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
        $partialMeta = $metaParser->parse($resource);
        $resource->meta = $partialMeta;
        return $resource;
    }
}



//$token = explode('---', $data);
//
//if (count($token) === 3) {
//	$properties = YAML::parse(trim($token[1]));
//	$content = $token[2];
//} else {
//	if (count($token) <= 2) {
//		$properties = ['title' => time()];
//		$content = $data;
//	} else {
//		$properties = YAML::parse(trim($token[1]));
//		$content = implode(array_splice($token, 2));
//	}
//}
