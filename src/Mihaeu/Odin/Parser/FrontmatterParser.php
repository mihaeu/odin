<?php

namespace Mihaeu\Odin\Parser;

use Mihaeu\Odin\Resource\Resource;
use Symfony\Component\Yaml\Yaml;

/**
 * FrontmatterParser
 *
 * Frontmatter is YAML embedded within the Frontmatter delimiters (---). E.g.
 *
 * ---
 * title: Awesome Post
 * date: 2014-01-01
 * ---
 *
 * @package Mihaeu\Odin\Resource\Parser
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class FrontmatterParser implements ParserInterface
{
    const DELIMITER = '---';

    /**
     * Checks if the content contains Frontmatter meta information, by checking
     * if the file starts with the Frontmatter delimiter or not.
     *
     * @param string $content
     *
     * @return bool
     */
    public static function isFrontmatter($content)
    {
        return strpos($content, FrontmatterParser::DELIMITER) === 0;
    }

    /**
     * Parses meta information from YAML format.
     *
     * @param Resource $resource
     *
     * @return array
     *
     * @throws ParserException
     */
    public function parse(Resource &$resource)
    {
        $tokens = preg_split('/'.FrontmatterParser::DELIMITER.'/', $resource->content, -1, PREG_SPLIT_NO_EMPTY);

        // there should be at least two tokens, one for the YAML part and one for the content
        // if there are more it's not a problem because the YAML delimiter was probably used
        // in the content. If the YAML delimiter has been used in the config, it should be escaped.
        if (count($tokens) > 1 && isset($tokens[0])) {
            // remove the meta information from the content
            // glue the leftovers using the delimiter, in case it was used before
            $resource->content = implode(FrontmatterParser::DELIMITER, array_slice($tokens, 1));
            $resource->metaType = Resource::META_TYPE_FRONTMATTER;
            return Yaml::parse($tokens[0]);
        } else {
            throw new ParserException();
        }
    }
}
