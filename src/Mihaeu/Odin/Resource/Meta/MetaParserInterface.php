<?php

namespace Mihaeu\Odin\Resource\Meta;

use Mihaeu\Odin\Resource\Resource;

interface MetaParserInterface
{
	public function parse(Resource &$resource);
}
