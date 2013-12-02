<?php

namespace Mihaeu\Odin\Resource\Content;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class PlainTransformer
 *
 * @package Mihaeu\Odin\Resource\Content
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class PlainTransformer implements ContentTransformerInterface
{
	public function transform(Resource &$resource)
	{
		$resource->contentType = Resource::CONTENT_TYPE_PLAIN;
		return $resource->content;
	}
}
