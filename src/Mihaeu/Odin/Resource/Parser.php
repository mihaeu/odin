<?php

namespace Mihaeu\Odin\Resource;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml\Yaml;

class Parser
{
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
	 * @param Resource $resource
	 */
	public function parse(Resource $resource)
	{
		$data = file_get_contents($resource->file->getRealPath());
		$token = explode('---', $data);

		if (count($token) === 3) {
			$properties = YAML::parse(trim($token[1]));
			$content = $token[2];
		} else {
			if (count($token) <= 2) {
				$properties = ['title' => time()];
				$content = $data;
			} else {
				$properties = YAML::parse(trim($token[1]));
				$content = implode(array_splice($token, 2));
			}
		}

		return new ParsedResource();
	}
}
