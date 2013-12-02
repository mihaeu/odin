<?php

namespace Mihaeu\Odin\Resource;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml\Yaml;

/**
 * The resource parser will figure out what meta type a
 * resource has and parse them appropriately.
 *
 * @package Mihaeu\Odin\Resource
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Parser
{
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
		foreach ($resources as $resource)
		{
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
		$metaParser = $this->getMetaParser($resource->content);

		$resource->meta = $metaParser->parse($resource);
		return $resource;
	}

	public function getMetaParser($content)
	{
		if (Meta\FrontmatterParser::isFrontmatter($content) === true)
		{
			return new Meta\FrontmatterParser();
		}
		// try conventions
		else
		{
			return new Meta\ConventionParser();
		}
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
