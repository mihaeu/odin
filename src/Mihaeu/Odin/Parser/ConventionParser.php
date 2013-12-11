<?php

namespace Mihaeu\Odin\Parser;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class ConventionParser
 *
 * @package Mihaeu\Odin\Resource\Parser
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class ConventionParser implements ParserInterface
{
    /**
     * Parses the filename for date and name conventions.
     *
     * @param Resource $resource
     *
     * @return array
     */
    public function parse(Resource &$resource)
    {
        $meta = [];
        $matches = [];
        if (preg_match('/(\d{4}-\d{2}-\d{2})-([\w\-_ ]+)\.\w+/', $resource->file->getFilename(), $matches)) {
            $date = \DateTime::createFromFormat('Y-m-d', $matches[1]);
            $meta['date'] = $date->getTimestamp();
            $meta['title'] = $matches[2];
        }

        $resource->metaType = Resource::META_TYPE_CONVENTION;
        return $meta;
    }
}
