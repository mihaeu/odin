<?php

namespace Mihaeu\Odin\Resource\Meta;

use Mihaeu\Odin\Resource\Resource;

interface MetaParser
{
	public function parse(Resource $resource);
}
