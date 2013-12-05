<?php

namespace Mihaeu\Odin\Transformer;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Transformer\PlainTransformer;
use Mihaeu\Odin\Transformer\TwigTransformer;
use Mihaeu\Odin\Transformer\MarkdownTransformer;

/**
 * Class TransformerFactory
 *
 * @package Mihaeu\Odin\Transformer
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class TransformerFactory
{
    public function getTransformer(Resource $resource)
    {
        if (MarkdownTransformer::isMarkdown($resource)) {
            return new MarkdownTransformer();
        } else {
            if (TwigTransformer::isTwig($resource)) {
                return new TwigTransformer();
            } else {
                return new PlainTransformer();
            }
        }
    }
}
