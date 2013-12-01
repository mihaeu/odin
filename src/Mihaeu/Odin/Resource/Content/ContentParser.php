<?php

namespace Mihaeu\Odin\Resource\Content;

use Mihaeu\Odin\Resource\Resource;

interface ContentParser
{
	public function parse(Resource $resource);
}
