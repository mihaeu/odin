<?php

namespace Mihaeu\Odin\Transformer;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class PlainTransformer
 *
 * @package Mihaeu\Odin\Resource\Transformer
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class PlainTransformer implements TransformerInterface
{
    public function transform(Resource &$resource)
    {
        $resource->contentType = Resource::CONTENT_TYPE_PLAIN;
        return $resource->content;
    }
}
