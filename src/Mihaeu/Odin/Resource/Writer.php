<?php

namespace Mihaeu\Odin\Resource;

/**
 * Class Writer
 *
 * @package Mihaeu\Odin\Resource
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Writer
{
	public function write(TransformedResource $resource)
	{
		file_put_contents($resource->destination, $resource->content);
	}

	public function writeAll(Array $resources)
	{
		foreach ($resources as $transformedResource)
		{
			$this->write($transformedResource);
		}
	}
}
