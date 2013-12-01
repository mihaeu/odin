<?php

namespace Mihaeu\Odin\Resource\Meta;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class FrontmatterParser
 * @package Mihaeu\Odin\Resource\Meta
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class FrontmatterParser implements MetaParser
{
	/**
	 * Checks if the content contains Frontmatter meta information.
	 *
	 * @param string $content
	 *
	 * @return bool
	 */
	public static function isFrontmatter($content)
	{
		return false;
	}

	public function parse(Resource $resource)
	{
		return [];
	}
}
