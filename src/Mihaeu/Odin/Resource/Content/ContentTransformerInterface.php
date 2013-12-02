<?php

namespace Mihaeu\Odin\Resource\Content;

use Mihaeu\Odin\Resource\Resource;

interface ContentTransformer
{
	public function parse(Resource $resource);
}
