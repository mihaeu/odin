<?php

namespace Mihaeu\Odin\Transformer;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Transformer\TransformerInterface;
use dflydev\markdown\MarkdownExtraParser;

class Transformer
{
    /**
     * @var TransformerFactory
     */
    private $transformerFactory;

    public function __construct(TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
    }

    /**
     * @param array $resources
     *
     * @return array
     */
    public function transformAll(Array $resources)
    {
        $transformedResources = [];
        foreach ($resources as $resource) {
            $transformedResources[] = $this->transform($resource);
        }
        return $transformedResources;
    }

    public function transform(Resource $resource)
    {
        $contentTransformer = $this->transformerFactory->getTransformer($resource);
        $resource->content = $contentTransformer->transform($resource);
        return $resource;
    }
}
