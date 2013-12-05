<?php

namespace Mihaeu\Odin\Transformer;

use Mihaeu\Odin\Resource\Resource;

interface TransformerInterface
{
    public function transform(Resource &$resource);
}
