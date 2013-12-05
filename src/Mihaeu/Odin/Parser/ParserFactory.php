<?php

namespace Mihaeu\Odin\Parser;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class ParserFactory
 *
 * @package Mihaeu\Odin\Parser
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class ParserFactory
{
    public function getParser(Resource $resource)
    {
        if (FrontmatterParser::isFrontmatter($resource->content) === true) {
            return new FrontmatterParser();
        } // try conventions
        else {
            return new ConventionParser();
        }
    }
}
