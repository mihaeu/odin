<?php

namespace Mihaeu\Odin\Resource\Content;

use Mihaeu\Odin\Resource\Resource;

interface ContentTransformerInterface
{
	public function transform(Resource &$resource);
}
