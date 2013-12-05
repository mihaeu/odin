<?php

namespace Mihaeu\Odin\Parser;

use Mihaeu\Odin\Resource\Resource;

interface ParserInterface
{
    public function parse(Resource &$resource);
}
